<?php

namespace Drupal\target\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\target\TargetInterface;
use Drupal\target\Controller\TargetControllerBase;

/**
 * Returns responses for Target Criteria module routes.
 */
class TargetCriteriaController extends ControllerBase {

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorage
   */
  protected $entityStorage;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new DomainControllerBase.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The storage controller.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityStorageInterface $entity_storage, EntityTypeManagerInterface $entity_manager) {
    $this->entityStorage = $entity_storage;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity_type.manager');
    return new static(
      $entity_manager->getStorage('target'),
      $entity_manager
    );
  }

  /**
   * Provides the target criteria submission form.
   *
   * @param \Drupal\target\TargetInterface $target
   *   A target entity.
   *
   * @return array
   *   Returns the target criteria submission form.
   */
  public function addCriteria(TargetInterface $target) {
    // The entire purpose of this controller is to add the values from
    // the parent target entity.
    $values['target'] = $target->id();

    // For now, we auto-set the label and group as well.
    $values['label'] = md5($target->id() . microtime());
    $values['group'] = 'AND_1';
    $values['logic'] = 'AND';
    // Create the entity and pass to the form.
    $criteria = \Drupal::entityTypeManager()->getStorage('target_criteria')->create($values);

    return $this->entityFormBuilder()->getForm($criteria);
  }

  /**
   * Provides the listing page for criteria.
   *
   * @param \Drupal\target\TargetInterface $target
   *   A target record entity.
   *
   * @return array
   *   A render array as expected by drupal_render().
   */
  public function listing(TargetInterface $target) {
    $list = \Drupal::entityTypeManager()->getListBuilder('target_criteria');
    $list->setTarget($target);
    return $list->render();
  }

  public function title(TargetInterface $target) {
    return $target->label();
  }

}
