<?php

/**
 * @file
 * Contains \Drupal\target\TargetCriteriaManagerInterface.
 */

namespace Drupal\target;

interface TargetCriteriaManagerInterface {

  /**
   * Gets a filtered list of plugins based on module dependencies.
   *
   * This method is used instead of getDefinitions() when creating UI elements, since
   * we want to filter out plugins with unmet dependencies. We keep the getDefinitions()
   * method intact in case it has other uses.
   *
   * @return array
   *   An array of plugins.
   */
  public function getPlugins();

  /**
   * Returns a single plugin definition.
   *
   * @param $id
   *   The plugin id.
   *
   * @return \Drupal\target\TargetCriteriaInterface;
   */
  public function getPlugin($id);
}
