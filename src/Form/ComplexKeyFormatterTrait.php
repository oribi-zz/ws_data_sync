<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Trait ComplexKeyFormatterTrait
 *
 * @package Drupal\ws_data_sync\Form
 */
trait ComplexKeyFormatterTrait {

  /**
   * @param $value
   *
   * @return string
   */
  public function toOption($value) {
    $option = '';
    foreach ($value as $part) {
      $option .= $option != '' ? ':' . $part : $part;
    }
    return $option;
  }

  /**
   * @param $data
   * @param $keys
   *
   * @return array
   */
  protected function toArray($data, $keys) {
    $values = explode(':', $data);
    $array = [];
    for ($i = 0; $i < count($keys); $i++) {
      $array[$keys[$i]] = $values[$i];
    }
    return $array;
  }

  protected function getConfigPropertySequenceMappingKeys(string $property, EntityTypeInterface $entity_type) {
    $plugin_id = $entity_type->getProvider() . '.' . $entity_type->id() . '.*';
    $entity_definition = \Drupal::service('config.typed')->getDefinition($plugin_id);
    return array_keys($entity_definition['mapping'][$property]['sequence']['mapping']);
  }


}