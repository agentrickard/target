<?php

namespace Drupal\target\Plugin\Condition;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\target\TargetCriteriaManagerInterface;
use Drupal\target\TargetInterface;
use Drupal\target\TargetConditionManager;

/**
 * Provides a 'Target' criteria.
 *
 * @Condition(
 *   id = "target",
 *   label = @Translation("Target")
 * )
 */
class Target extends ConditionPluginBase implements ContainerFactoryPluginInterface, CacheableDependencyInterface {

  /**
   * The target storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $targetStorage;

  /**
   * The criteria storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $criteriaStorage;

  /**
   * The criteria manager.
   *
   * @var \Drupal\target\TargetCriteriaManagerInterface
   */
  protected $criteriaManager;

  /**
   * The condition manager.
   *
   * @var \Drupal\target\TargetConditionManager
   */
  protected $conditionManager;

  /**
   * The active targets.
   */
  protected $targets;

  /**
   * The active cache contexts.
   */
  protected $contexts;

  /**
   * The time-sensitive criteria.
   */
  protected $timedConditions;

  /**
   * Constructs a Target criteria plugin.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager class.
   * @param \Drupal\target\TargetCriteriaManagerInterface $criteria_manager
   *   The criteria plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $condition_manager
   *   The condition plugin manager.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(EntityManagerInterface $entity_manager, TargetCriteriaManagerInterface $criteria_manager,  PluginManagerInterface $condition_manager, array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->targetStorage = $entity_manager->getStorage('target');
    $this->criteriaStorage = $entity_manager->getStorage('target_criteria');
    $this->criteriaManager = $criteria_manager;
    $this->conditionManager = $condition_manager;
    $this->targets = $this->targetStorage->loadMultiple();
    $this->contexts = array();
    $this->timedConditions = array();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
        $container->get('entity.manager'),
        $container->get('plugin.manager.target.criteria'),
        $container->get('plugin.manager.condition'),
        $configuration,
        $plugin_id,
        $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['targets'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('When the following targets are active'),
      '#default_value' => $this->configuration['targets'],
      '#options' => array_map('\Drupal\Component\Utility\Html::escape', $this->getOptions()),
      '#description' => $this->t('If you select no targets, the criteria will evaluate to TRUE for all requests.'),
      '#attached' => array(
        'library' => array(
          'target/drupal.target',
        ),
      ),
    );
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'targets' => array(),
    ) + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['targets'] = array_filter($form_state->getValue('targets'));
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // Use the target labels. They will be sanitized below.
    $targets = array_intersect_key($this->getOptions(), $this->configuration['targets']);
    if (count($targets) > 1) {
      $targets = implode(', ', $targets);
    }
    else {
      $targets = reset($targets);
    }
    if ($this->isNegated()) {
      return $this->t('Active target is not @targets', array('@targets' => $targets));
    }
    else {
      return $this->t('Active target is @targets', array('@targets' => $targets));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $ids = $this->configuration['targets'];
    if (empty($ids) && !$this->isNegated()) {
      return TRUE;
    }
    // No context found?
    elseif (empty($ids)) {
      return FALSE;
    }

    // Load the targets.
    $targets = $this->targetStorage->loadMultiple($ids);
    uasort($targets, [$this, 'sort']);

    // Load the criteria for the target into logical groups.
    foreach ($targets as $target) {
      $criteria = $this->getCriteria($target);
      $groups = $this->groupCriteria($criteria);
    }
    // Query the plugin for active status.
    $return = FALSE;
    foreach ($groups as $group => $logic) {
      $applies = FALSE;
      foreach ($logic as $operator => $items) {
        foreach ($items as $item) {
          // Handles conditions.
          $condition = $this->conditionManager->createInstance($item->getPlugin());
          $applies = $condition->evaluate();
          $this->addCacheContexts($condition->getCacheContexts());
          if ($operator == 'AND') {
            $return = $applies;
          }
          elseif ($operator == 'OR' && !empty($applies)) {
            $return = $applies;
          }
        }
      }
    }

    // NOTE: The context system handles negation for us.
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts += $this->contexts;
    return $contexts;
  }

  /**
   * Returns an options list of targets.
   *
   * @return array
   *   An array of id => label for use in forms.
   */
  private function getOptions() {
    $options = [];
    uasort($this->targets, [$this, 'sort']);
    foreach ($this->targets as $target) {
      $options[$target->id()] = $target->label();
    }
    return $options;
  }

  /**
   * Sorts target options by weight and label.
   */
  private function sort($a, $b) {
    if ($a->getWeight() > $b->getWeight()) {
      return 1;
    }
    if ($a->label() > $b->label()) {
      return 1;
    }
    return 0;
  }

  /**
   * Returns an array of criteria that define a target.
   *
   * @param \Drupal\target\TargetInterface $target
   *   The target being loaded.
   *
   * @return array
   *   An array of target criteria. \Drupal\target\TargetCriteriaInterface
   */
  private function getCriteria(TargetInterface $target) {
    $criteria = [];
    $query = $this->criteriaStorage->getQuery()
      ->condition('target', $target->id())
      ->sort('weight');
    $ids = $query->execute();
    if (!empty($ids)) {
      $criteria = $this->criteriaStorage->loadMultiple($ids);
      uasort($criteria, [$this, 'sort']);
    }
    return $criteria;
  }

  /**
   * Split our list of criteria into logical groups.
   *
   * @param array $criteria
   *
   * @return array
   */
  public function groupCriteria($criteria) {
    $groups = [];
    foreach ($criteria as $item) {
      $groups[$item->getGroup()][$item->getLogic()][] = $item;
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  public function addCacheContexts(array $contexts) {
    $this->contexts += $contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Note that these cache settings apply to the access method on each block.
    // Default no cache.
    $age = -1;
    foreach ($this->timedConditions as $time) {
      if ($age == -1) {
        $age = $time;
      }
      elseif ($time < $age) {
        $age = $time;
      }
    }
    return $age;
  }

}
