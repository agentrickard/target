<?php

namespace Drupal\target;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the target entity type.
 *
 * @see \Drupal\target\Entity\Target.
 */
class TargetAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Handle known operations.
    if ($operation == 'view' || $operation == 'delete' || $operation == 'update') {
      return AccessResult::allowedIf($account->hasPermission('administer targets'))->cachePerPermissions();
    }
    // Handle unknown operations.
    return parent::checkAccess($entity, $operation, $account);
  }

}
