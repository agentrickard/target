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
class TargetBlockListController extends ControllerBase {

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
