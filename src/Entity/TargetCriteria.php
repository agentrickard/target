<?php

namespace Drupal\target\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\target\TargetCriteriaInterface;

/**
 * Defines the target criteria entity.
 *
 * @ConfigEntityType(
 *   id = "target_criteria",
 *   label = @Translation("Target criteria"),
 *   handlers = {
 *     "access" = "Drupal\target\TargetAccessControlHandler",
 *     "list_builder" = "Drupal\target\TargetCriteriaListBuilder",
 *     "form" = {
 *       "add" = "Drupal\target\TargetCriteriaForm",
 *       "edit" = "Drupal\target\TargetCriteriaForm",
 *       "default" = "Drupal\target\TargetCriteriaForm",
 *       "delete" = "Drupal\target\Form\TargetCriteriaDeleteForm"
 *     }
 *   },
 *   config_prefix = "target_criteria",
 *   admin_permission = "administer targets",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "weight" = "weight"
 *   },
 *   links = {
 *     "delete-form" = "/admin/config/target/criteria/delete/{target_criteria}",
 *     "edit-form" = "/admin/config/target/criteria/edit/{target_criteria}",
 *     "collection" = "/admin/config/target/criteria/{target}",
 *     "canonical" = "/admin/config/target/criteria/edit/{target_criteria}",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target",
 *     "plugin",
 *     "group",
 *     "logic",
 *     "operator",
 *     "values",
 *     "weight",
 *   }
 * )
 */
class TargetCriteria extends ConfigEntityBase implements TargetCriteriaInterface {

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
   * The plugin prividing the criteria.
   *
   * @var string
   */
  protected $plugin;

  /**
   * The criteria group.
   *
   * @var int
   */
  protected $group;

  /**
   * The criteria logic for testing criteria (AND/OR).
   *
   * @var string
   */
  protected $logic;

  /**
   * The criteria operator for testing criteria (= / <>).
   *
   * @var string
   */
  protected $operator;

  /**
   * The assigned values of the criteria.
   *
   * @var array
   */
  protected $values;

  /**
   * The sort order.
   *
   * @var integer
   */
  protected $weight;

  /**
   * {@inheritdoc}
   */
  public function getTarget() {
    return $this->target;
  }

  /**
   * {@inheritdoc}
   */
  public function setTarget($id) {
    $this->target = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    return $this->plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function setPlugin($plugin) {
    $this->plugin = $plugin;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * {@inheritdoc}
   */
  public function setGroup($group) {
    $this->group = $group;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogic() {
    return $this->logic;
  }

  /**
   * {@inheritdoc}
   */
  public function setLogic($logic) {
    $this->logic = $logic;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperator() {
    return $this->operator;
  }

  /**
   * {@inheritdoc}
   */
  public function setOperator($operator) {
    $this->operator = $operator;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function setValues($values) {
    $this->values = $values;
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
