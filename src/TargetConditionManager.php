<?php

/**
 * @file
 * Contains \Drupal\target\TargetConditionManager.
 */

namespace Drupal\target;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\target\TargetCriteriaInterface;

class TargetConditionManager extends DefaultPluginManager {

  /**
   * Constructs a new TargetConditionManager.
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
    parent::__construct('Plugin/Condition', $namespaces, $module_handler, 'Drupal\Core\Plugin\ContainerFactoryPluginInterface', 'Drupal\Core\Condition\Annotation\Condition');
    $this->alterInfo('target_condition_info');
    $this->setCacheBackend($cache_backend, 'target_condition_plugins');
    $this->moduleHandler = $module_handler;
  }

  public function getConfiguration(TargetCriteriaInterface $criteria, $plugin) {
    $this->plugin = $plugin;
    $configuration = [];
    $configuration['negate'] = (bool) ($criteria->getOperator() == '<>');
    $keys = $this->getValueKey();
    $type = $this->getDataType();
    foreach ($keys as $key) {
      $configuration[$key] = ($type == 'array') ? $criteria->getValues() : implode('', $criteria->getValues());
    }
    return $configuration;
  }

  public function getValueKey() {
    $config = $this->plugin->defaultConfiguration();
    if (isset($config['negate'])) {
      unset($config['negate']);
    }
    return array_keys($config);
  }

  public function getDataType() {
    $config = $this->plugin->defaultConfiguration();
    // This is brittle.
    $key = current($config);
    return gettype($key);
  }

}
