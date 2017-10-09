<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ws_data_sync\Entity\Feed;

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

  protected function getConfigPropertySequenceMappingKeys(string $property, string $entity_type) {
//    $entity->getTypedData()->getProperties();
//    $entity_schema_id = $entity->getSchemaIdentifier();
    $entity_definition = \Drupal::service('config.typed')->getDefinition('ws_data_sync.' . $entity_type . '.*');
    return array_keys($entity_definition['mapping'][$property]['sequence']['mapping']);
  }


}