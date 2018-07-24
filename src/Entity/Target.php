<?php

namespace Drupal\target\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\target\TargetInterface;

/**
 * Defines the target entity.
 *
 * @ConfigEntityType(
 *   id = "target",
 *   label = @Translation("Target"),
 *   handlers = {
 *     "access" = "Drupal\target\TargetAccessControlHandler",
 *     "list_builder" = "Drupal\target\TargetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\target\TargetForm",
 *       "edit" = "Drupal\target\TargetForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "target",
 *   admin_permission = "administer targets",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "delete-form" = "/admin/config/target/delete/{target}",
 *     "edit-form" = "/admin/config/target/edit/{target}",
 *     "collection" = "/admin/config/target",
 *     "canonical" = "/admin/config/target/edit/{target}",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "weight",
 *   }
 * )
 */
class Target extends ConfigEntityBase implements TargetInterface {

  /**
   * The form ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable label of the target.
   *
   * @var string
   */
  protected $label;

  /**
   * The description of the target.
   *
   * @var string
   */
  protected $description;

  /**
   * The sort order.
   *
   * @var integer
   */
  protected $weight;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

}
