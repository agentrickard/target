<?php

namespace Drupal\target;

use Drupal\target\TargetInterface;
use Drupal\target\TargetCriteriaInterface;

/**
 * Defines the plugin interface for target criteria.
 */
interface TargetCriteriaPluginInterface {

  /**
   * Returns the id for a plugin.
   *
   * @return string
   */
  public function id();

  /**
   * Returns the label for a plugin.
   *
   * @return string
   */
  public function label();

  /**
   * Returns the status of a plugin.
   *
   * @return boolean
   */
  public function status();

  /**
   * Checks if a criteria matches applies to a given context.
   *
   * @param Drupal\target\TargetCriteriaInterface $criteria
   *   The criteria being checked.
   *
   * @return boolean
   */
  public function applies(TargetCriteriaInterface $criteria);

  /**
   * Gets the options for a plugin.
   *
   * @return array
   *   In the format id => label.
   */
  public function options();

  /**
   * Returns if multiple selections are allowed; defaults to TRUE.
   *
   * @return boolean
   */
  public function multiple();

  /**
   * Gets the comparison operators for a plugin.
   *
   * @return array
   *   In the format id => label.
   */
  public function operators();

  /**
   * Provides configuration options.
   *
   * @param Drupal\target\TargetCriteriaInterface $criteria;
   *   The target the criteria is attached to.
   */
  public function configForm(TargetCriteriaInterface $criteria);

  /**
   * Gets the cache contexts that apply to a criteria.
   *
   * @return array
   *   An array of cacheContexts.
   */
  public function cacheContexts();

  /**
   * Gets the max cache time of the criteria.
   *
   * @return int
   */
  public function getMaxAge(TargetCriteriaInterface $criteria);

}
