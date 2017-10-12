<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Trait ComplexKeyFormatterTrait
 * Enables conversion of complex property values in config entity select form elements
 * "#type => select" form elements specifying the (custom) "#complex" attribute
 *
 * @section Sample code
 * Here's an example of the structure required inside your YAML file to utilize this trait:
 * @code
 * my_module.my_entity.*:
 *   type: config_entity
 *   label: 'My config entity'
 *   mapping:
 *     id:
 *       type: string
 *       label: 'ID'
 *     ...
 *     my_complex_property:
 *       type: sequence
 *       label: 'My complex property'
 *       sequence:
 *         type: mapping
 *         mapping:
 *           sub_property_1:
 *             type: string
 *             label: 'Sub property 1'
 *           sub_property_2:
 *             type: string
 *             label: 'Sub property 2'
 *           ...
 *     another_property:
 *       type: string
 *       label: 'Another property'
 *     ...
 * @endcode
 *
 * The #default_value of #complex form elements is set using the convertSequencedPropertyToString() method
 * Also, values in #complex form elements require a specific formatting for the trait to function
 *
 * Using the YAML snippet above the form element could look something like this:
 * @code
 * $form['my_complex_property'] = [
 *  '#type' => 'select',
 *  '#title' => t('Select an option'),
 *  '#options' => [
 *    'value_a1:value_a2' => 'Option A',
 *    'value_b1:value_b2' => 'Option B',
 *    'value_c1:value_c2' => 'Option C',
 *  ],
 *  '#default_value' => is_array($this->entity->getMyComplexProperty())
 *    ?? self::convertSequencedPropertyToString($this->entity->getMyComplexProperty()),
 *  '#complex' => TRUE,
 * ];
 * @endcode
 *
 * Saving structured data using #complex for elements is accomplished by calling the setComplexValues method inside the forms save method
 * @code
 * public function save(array $form, FormStateInterface $form_state) {
 * ...
 *  $format_complex = self::setComplexValues($this->entity, $form, $form_state);
 *  ...
 *  $status = $field_mapping->save();
 *  ...
 * }
 *
 *
 *
 * @endcode
 *
 * @section Use cases
 * - Saving hierarchy data when using multilevel select form elements
 * @see \Drupal\ws_data_sync\EntityTypeMapper::getContentEntityTypes()
 * @see \Drupal\ws_data_sync\Form\FeedForm
 * - Saving extended data inside one select option
 * @see \Drupal\ws_data_sync\EntityFieldMapper::getEntityFields()
 * @see \Drupal\ws_data_sync\Form\FieldMappingForm
 *
 * @package Drupal\ws_data_sync\Form
 */
trait ComplexKeyFormatterTrait {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $form
   * @param $form_state
   *
   * @return bool
   */
  protected function setComplexValues(EntityInterface $entity, $form, $form_state) {
    foreach ($form as $id => $element) {
      if (isset($element['#complex'])) {
        // Massage "complex" (colon separated) value to array for structured config storage
        $keys = self::getConfigPropertySequenceMappingKeys($id, $entity->getEntityType());
        if (count($keys) == count(explode(':', $form_state->getValue($id)))) {
          $entity->set($id, self::mapStringToSequencedProperty($form_state->getValue($id), $keys));
        } else {
          throw new \InvalidArgumentException(sprintf('The structure of the value submitted to the %s field does not match the mapping sequence defined in the entities YAML file', $id));
        }
      }
    }
    return TRUE;
  }

  /**
   * @param $sequenced_property
   *
   * @return string
   */
  protected function convertSequencedPropertyToString($sequenced_property) {
    $option = '';
    foreach ($sequenced_property as $part) {
      $option .= $option != '' ? ':' . $part : $part;
    }
    return $option;
  }

  /**
   * @param $string
   * @param $sequence_keys
   *
   * @return array
   */
  protected function mapStringToSequencedProperty($string, $sequence_keys) {
//  protected function toArray($data, $keys) {
    $values = explode(':', $string);
    $array = [];
    for ($i = 0; $i < count($sequence_keys); $i++) {
      $array[$sequence_keys[$i]] = $values[$i];
    }
    return $array;
  }

  /**
   * @param string $property
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *
   * @return array
   */
  protected function getConfigPropertySequenceMappingKeys(string $property, EntityTypeInterface $entity_type) {
    $plugin_id = $entity_type->getProvider() . '.' . $entity_type->id() . '.*';
    $entity_definition = \Drupal::service('config.typed')->getDefinition($plugin_id);
    return array_keys($entity_definition['mapping'][$property]['sequence']['mapping']);
  }

}