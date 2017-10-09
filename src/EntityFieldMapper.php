<?php

namespace Drupal\ws_data_sync;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\ws_data_sync\Plugin\SpecialFieldMapAdapterManager;

/**
 * Class EntityFieldMapper.
 */
class EntityFieldMapper {

  // Todo: move this array into module configuration
  /**
   * @var array
   */
  protected $excluded_fields = [
    'nid',
    'uuid',
    'vid',
    'langcode',
    'type',
    'status',
    'uid',
    'created',
    'changed',
    'promote',
    'sticky',
    'revision_timestamp',
    'revision_uid',
    'revision_log',
    'revision_translation_affected',
    'default_langcode',
    'path',
  ];


  /**
   * Drupal\Core\Entity\EntityFieldManager definition.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * @var \Drupal\ws_data_sync\Plugin\SpecialFieldMapAdapterManager
   */
  private $specialFieldMapAdapter;

  /**
   * Constructs a new EntityFieldMapper object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManager $entity_field_manager
   * @param \Drupal\ws_data_sync\Plugin\SpecialFieldMapAdapterManager $special_field_map_adapter
   */
  public function __construct(EntityFieldManager $entity_field_manager, SpecialFieldMapAdapterManager $special_field_map_adapter) {
    $this->entityFieldManager = $entity_field_manager;
    $this->specialFieldMapAdapter = $special_field_map_adapter;
  }

  public function getEntityFields($type, $bundle) {
    $fields = [];
    foreach ($this->entityFieldManager->getFieldDefinitions($type, $bundle) as $id => $definition) {
      if (!in_array($id, $this->excluded_fields)) {
        $fields[$id] = $definition->getLabel();
      }
    }
    return $fields;
  }

}
