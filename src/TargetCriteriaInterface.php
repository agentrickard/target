<?php

namespace Drupal\target;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a target criteria entity.
 */
interface TargetCriteriaInterface extends ConfigEntityInterface {

  /**
   * Returns the target id associated with this criteria.
   *
   * @return string
   *   A target id.
   */
  public function getTarget();

  /**
   * Sets the target id to be stored with this criteria.
   *
   * @param string $id
   *   The target id the criteria is assigned to.
   *
   * @return $this
   */
  public function setTarget($id);

  /**
   * Returns the plugin for this criteria.
   *
   * @return string
   *   A criteria plugin.
   */
  public function getPlugin();

  /**
   * Sets the plugin for this criteria.
   *
   * @param string $plugin
   *   The plugin of this criteria.
   *
   * @return $this
   */
  public function setPlugin($plugin);

  /**
   * Returns the group for this criteria.
   *
   * @return string
   *   A criteria group.
   */
  public function getGroup();

  /**
   * Sets the group for this criteria.
   *
   * @param string $group
   *   The group of this criteria.
   *
   * @return $this
   */
  public function setGroup($group);

  /**
   * Returns the logic for this criteria.
   *
   * @return string
   *   A criteria logic.
   */
  public function getLogic();

  /**
   * Sets the logic for this criteria.
   *
   * @param string $logic
   *   The logic of this criteria.
   *
   * @return $this
   */
  public function setLogic($logic);

  /**
   * Returns the operator for this criteria.
   *
   * @return string
   *   A criteria operator.
   */
  public function getOperator();

  /**
   * Sets the operator for this criteria.
   *
   * @param string $operator
   *   The operator of this criteria.
   *
   * @return $this
   */
  public function setOperator($operator);

  /**
   * Returns the values for this criteria.
   *
   * @return string
   *   A criteria values.
   */
  public function getValues();

  /**
   * Sets the values for this criteria.
   *
   * @param string $values
   *   The values of this criteria.
   *
   * @return $this
   */
  public function setValues($values);

  /**
   * Returns the weight of this target (used for sorting).
   *
   * @return int
   *   The weight of this category.
   */
  public function getWeight();

  /**
   * Sets the weight.
   *
   * @param int $weight
   *   The desired weight.
   *
   * @return $this
   */
  public function setWeight($weight);

}
