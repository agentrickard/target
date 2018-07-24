<?php

namespace Drupal\target;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for target edit forms.
 */
class TargetForm extends EntityForm {

  /**
   * The target entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a TargetForm object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity type manager.
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('target')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\target\Entity\Target $target */
    $target = $this->entity;
    $targets = $this->storage->loadMultiple();

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#size' => 40,
      '#maxlength' => 80,
      '#default_value' => $target->label(),
      '#description' => $this->t('The human-readable name is shown in target lists and forms.'),
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $target->id(),
      '#machine_name' => array(
        'source' => array('label'),
        'exists' => array($this->storage, 'load'),
      ),
    );
    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
      '#size' => 40,
      '#maxlength' => 80,
      '#default_value' => $target->getDescription(),
      '#description' => $this->t('A short description of the target.'),
    );
    $next = count($targets) + 1;
    $form['weight'] = array(
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#required' => TRUE,
      '#delta' => $next,
      '#default_value' => $target->getWeight() ?: $next,
      '#description' => $this->t('The sort order for this record. Lower values display first.'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $target = $this->entity;
    if ($target->isNew()) {
      drupal_set_message($this->t('Target record created.'));
    }
    else {
      drupal_set_message($this->t('Target record updated.'));
    }
    $target->save();
    $form_state->setRedirect('target.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array &$form, FormStateInterface $form_state) {
    $target = $this->entity;
    $target->delete();
    $form_state->setRedirect('target.collection');
  }

}
