<?php

/**
 * @file
 * Contains \Drupal\target\TargetCriteriaManager.
 */

namespace Drupal\target;

use Drupal\target\TargetCriteriaManagerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

class TargetCriteriaManager extends DefaultPluginManager implements TargetCriteriaManagerInterface {

  /**
   * Constructs a new TargetCriteriaManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/TargetCriteria', $namespaces, $module_handler, 'Drupal\target\TargetCriteriaPluginInterface', 'Drupal\target\Annotation\TargetCriteria');
    $this->alterInfo('target_criteria_info');
    $this->setCacheBackend($cache_backend, 'target_plugins');
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugins() {
    $plugins = array();
    $definitions = $this->getDefinitions();
    // @TODO: get plugins from other sources.
    // @see TargetCriteriaForm.php
    foreach ($definitions as $info) {
      $plugins[$info['id']] = $info['label']->render();
      foreach($info['modules'] as $module) {
        if (!$this->moduleHandler->moduleExists($module)) {
          unset($plugins[$info['id']]);
        }
      }
    }

    return $plugins;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin($id) {
    return $this->createInstance($id);
  }
}
