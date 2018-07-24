<?php

namespace Drupal\target\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete a target record.
 */
class TargetCriteriaDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', array('%name' => $this->entity->getPlugin()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('target.criteria_collection', ['target' => $this->entity->getTarget()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Criteria %plugin has been deleted.', array('%plugin' => $this->entity->getPlugin())));
    \Drupal::logger('target')->notice('Criteria %plugin has been deleted.', array('%plugin' => $this->entity->getPlugin()));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
