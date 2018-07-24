<?php

namespace Drupal\target;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a target entity.
 */
interface TargetInterface extends ConfigEntityInterface {

  /**
   * Returns the description to be displayed to user.
   *
   * @return string
   *   A target description.
   */
  public function getDescription();

  /**
   * Returns the weight of this target (used for sorting).
   *
   * @return int
   *   The weight of this category.
   */
  public function getWeight();

  /**
   * Sets the description to be displayed to the user.
   *
   * @param string $description
   *   The description of this target.
   *
   * @return $this
   */
  public function setDescription($description);

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
