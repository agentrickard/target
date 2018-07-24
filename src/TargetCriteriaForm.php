<?php

namespace Drupal\target;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\target\TargetCriteriaManagerInterface;

/**
 * Base form for criteria edit forms.
 */
class TargetCriteriaForm extends EntityForm {

  /**
   * The target_criteria entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The target_criteria plugin manager.
   *
   * @var \Drupal\target\TargetCriteriaManagerInterface
   */
  protected $manager;

  /**
   * Constructs a TargetForm object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity type manager.
   * @param \Drupal\target\TargetCriteriaManagerInterface $manager
   *   The plugin manager.
   */
  public function __construct(EntityStorageInterface $storage, TargetCriteriaManagerInterface $manager) {
    $this->storage = $storage;
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('target_criteria'),
      $container->get('plugin.manager.target.criteria')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\target\Entity\TargetCriteria $criteria */
    $criteria = $this->entity;
    $all_criteria = $this->storage->loadMultiple();
    $plugins = $this->manager->getPlugins();
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#size' => 40,
      '#maxlength' => 80,
      '#default_value' => $criteria->isNew() ? '' : $criteria->label(),
      '#description' => $this->t('The human-readable name is shown in criteria lists and forms.'),
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $criteria->isNew() ? '' : $criteria->id(),
      '#machine_name' => array(
        'source' => array('label'),
        'exists' => array($this->storage, 'load'),
      ),
    );
    $form['target'] = array(
      '#type' => 'value',
      '#value' => $criteria->getTarget(),
    );
    $form['group'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Group'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#size' => 40,
      '#maxlength' => 80,
      '#default_value' => $criteria->getGroup(),
      '#description' => $this->t('The criteria group.'),
    );
    $form['logic'] = array(
      '#type' => 'select',
      '#title' => $this->t('Logic'),
      '#required' => TRUE,
      '#options' => ['AND' => $this->t('AND'), 'OR' => $this->t('OR')],
      '#default_value' => $criteria->getLogic(),
      '#description' => $this->t('The criteria logic.'),
    );
    $form['plugin'] = array(
      '#type' => 'select',
      '#title' => $this->t('Criteria type'),
      '#required' => TRUE,
      '#options' => $plugins,
      '#disabled' => !$criteria->isNew(),
      '#default_value' => $criteria->getPlugin(),
      '#description' => $this->t('The type of criteria to add.'),
    );
    // We read conditions.
    $conditions = \Drupal::service('plugin.manager.condition');
    foreach ($conditions->getDefinitions() as $key => $definition) {
      if ($key != 'target') {
        $form['plugin']['#options'][$key] = $definition['label']->render();
        $handler = $conditions->createInstance($key);
        $element = $handler->buildConfigurationForm(array(), $form_state);
        $form[$key] = array(
          '#type' => 'container',
          '#states' => array(
            'visible' => array(
              ':input[name=plugin]' => array('value' => $key),
            ),
          ),
        );
        $form[$key][$key . ':operator'] = [
          '#type' => 'select',
          '#title' => $this->t('Comparison operator'),
          '#default_value' => $criteria->getOperator(),
          '#options' => [
            '=' => $this->t('Equal to'),
            '<>' => $this->t('Not equal to'),
          ],
        ];
        // @TODO: This is brittle, assuming that the config element is the first part of
        // the array is poor logic.
        $field = current($element);
        $form[$key][$key . ':values'] = $field;
        $values = $criteria->getValues();
        if (in_array($field['#type'], ['textfield', 'textarea'])) {
          if (!empty($values)) {
            $values = implode('', $values);
          }
        }
        elseif (is_null($values)) {
          $values = [];
        }
        $form[$key][$key . ':values']['#default_value'] = $values;
      }
    }
    // Handle our own plugins.
    foreach ($plugins as $id => $label) {
      $plugin = $this->manager->getPlugin($id);
      $form += $plugin->configForm($criteria);
    }
    $next = count($all_criteria) + 1;
    $form['weight'] = array(
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#required' => TRUE,
      '#delta' => $next,
      '#default_value' => $criteria->getWeight() ?: $next,
      '#description' => $this->t('The sort order for this record. Lower values display first.'),
    );

    $form = parent::form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $condition = $this->entity;
    // We prefix the operator and value form elements to prevetn validation errors
    // since the names must be unique.
    $values = $form_state->getValues();
    $plugin = $form_state->getValue('plugin');
    $operator = $plugin . ':operator';
    $value = $plugin . ':values';
    if (isset($values[$operator])) {
      $condition->setOperator($values[$operator]);
    }
    if (isset($values[$value])) {
      $stored = $values[$value];
      if (!is_array($stored)) {
        $stored = array($values[$value]);
      }
      else {
        $stored = array_filter($stored);
      }
      $condition->setValues($stored);
    }
    if ($condition->isNew()) {
      drupal_set_message($this->t('Target criteria created.'));
    }
    else {
      drupal_set_message($this->t('Target criteria updated.'));
    }
    $condition->save();
    $form_state->setRedirect('target.criteria_collection', array('target' => $condition->target));
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array &$form, FormStateInterface $form_state) {
    $condition = $this->entity;
    $condition->delete();
    $form_state->setRedirect('target.criteria_collection', array('target' => $condition->target));
  }

}
