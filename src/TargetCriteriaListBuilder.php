<?php

namespace Drupal\target;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\target\TargetInterface;

/**
 * User interface for the target overview screen.
 */
class TargetCriteriaListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'target_criteria';

  /**
   * Name of the entity's weight field or FALSE if no field is provided.
   *
   * @var string|bool
   */
  protected $weightKey = 'weight';

  /**
   * Sets the target context for this list.
   *
   * @param \Drupal\target\TargetInterface $target
   *   The target to set as context for the list.
   */
  public function setTarget(TargetInterface $target) {
    $this->target = $target;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'target_criteria_overview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['plugin'] = $this->t('Plugin');
    $header['group'] = $this->t('Group');
    $header['logic'] = $this->t('Logic');
    $header['operator'] = $this->t('Operator');
    $header['values'] = $this->t('Values');
    $header += parent::buildHeader();
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    // @TODO: Sanitize output.
    $row['label'] = $this->getLabel($entity);
    // @TODO: replace with plugin label.

    $row['plugin'] = ['#markup' => $entity->getPlugin()];
    $row['group'] = ['#markup' => $entity->getGroup()];
    $row['logic'] = ['#markup' => $entity->getLogic()];
    $row['operator'] = ['#markup' => $entity->getOperator()];
    $string = implode(', ', $entity->getValues());
    if (strlen($string) > 40) {
      $string = substr($string, 0, 40) . '...';
    }
    $row['values'] = ['#markup' => htmlentities($string)];
    $row += parent::buildRow($entity);
    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $groups = $this->getCriteriaGroups();
    if (count($groups) > 1) {
      // Do something with the form.
    }
    $form[$this->entitiesKey]['#targets'] = $this->entities;
    $form['actions']['submit']['#value'] = $this->t('Save configuration');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Configuration saved.'));
  }

  /**
   * {@inheritdoc}
   *
   * See Drupal\Core\Entity\EntityListBuilder::getEntityIds()
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->condition('target', $this->target->id())
      ->sort($this->entityType->getKey('weight'));
    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * Gets the groups used by this target.
   */
  protected function getCriteriaGroups() {
    $groups = [];
    foreach ($this->entities as $criteria) {
      $name = $criteria->getGroup();
      $values = explode('_', $name);
      $groups[$name] = [
        'criteria' => $values[0],
        'weight' => $values[1]
      ];
    }
    return $groups;
  }

}
