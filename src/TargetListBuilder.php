<?php

namespace Drupal\target;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * User interface for the target overview screen.
 */
class TargetListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  protected $entitiesKey = 'targets';

  /**
   * Name of the entity's weight field or FALSE if no field is provided.
   *
   * @var string|bool
   */
  protected $weightKey = 'weight';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'target_overview_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations['criteria'] = array(
      'title' => $this->t('Criteria'),
      'url' => Url::fromRoute('target.criteria_collection', array('target' => $entity->id())),
      'weight' => 5,
    );
    $operations += parent::getOperations($entity);
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['description'] = $this->t('Description');
    $header += parent::buildHeader();
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    $row['description'] = ['#markup' => $entity->getDescription()];
    $row += parent::buildRow($entity);
    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
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

}
