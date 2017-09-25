<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 13-09-2017
 * Time: 01:29
 */

namespace Drupal\ws_data_sync\Form;


use Drupal\Core\Entity\EntityInterface;

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

  protected function getConfigPropertySequenceMappingKeys(string $property, EntityInterface $entity) {
    $entity_schema_id = $entity->getSchemaIdentifier();
    $entity_definition = \Drupal::service('config.typed')->getDefinition($entity_schema_id);
    return array_keys($entity_definition['mapping'][$property]['sequence']['mapping']);
  }


}