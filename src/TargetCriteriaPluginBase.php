<?php

/**
 * @file
 * Contains \Drupal\target\TargetCriteriaPluginBase.
 */

namespace Drupal\target;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\target\TargetCriteria\PluginInterface;

/**
 * Base methods for use with target criteria plugins.
 */
abstract class TargetCriteriaPluginBase extends PluginBase implements TargetCriteriaPluginInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->pluginDefinition['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

  /**
   * @inheritdoc
   */
  public function options() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function status() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

  /**
   * @inheritdoc
   */
  public function operators() {
    return [
      '=' => $this->t('Equal to'),
      '<>' => $this->t('Not equal to'),
      '>' => $this->t('Greater than'),
      '<' => $this->t('Less than'),
      '>=' => $this->t('Greater than or equal to'),
      '<=' => $this->t('Less than or equal to'),
    ];
  }

  /**
   * @inheritdoc
   */
  public function applies(TargetCriteriaInterface $criteria) {
    return TRUE;
  }

  /**
   * @inheritdoc
   */
  public function configForm(TargetCriteriaInterface $criteria) {
    $form[$this->id()] = array(
      '#type' => 'container',
      '#states' => array(
        'visible' => array(
          ':input[name=plugin]' => array('value' => $this->id()),
        ),
      ),
    );
    $form[$this->id()][$this->id() . ':operator'] = array(
      '#type' => 'select',
      '#title' => $this->t('Comparison operator'),
      '#options' => $this->operators(),
      '#default_value' => $criteria->getOperator(),
    );
    $form[$this->id()][$this->id() . ':values'] = array(
      '#type' => 'select',
      '#title' => $this->t('Value'),
      '#options' => $this->options(),
      '#multiple' => $this->multiple(),
      '#size' => ($this->multiple() && $this->options() >= 10) ? 10 : NULL,
      '#default_value' => $criteria->getValues(),
    );
    return $form;
  }

  /**
   * @inheritdoc
   */
  public function cacheContexts() {
    return [];
  }

  /**
   * @inheritdoc
   */
  public function getMaxAge(TargetCriteriaInterface $criteria) {
    return -1;
  }

}
