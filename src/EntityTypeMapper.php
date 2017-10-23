<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfo;

/**
 * Class EntityTypeMapper.
 */
class EntityTypeMapper {

  // Todo: move this array into module configuration
  /**
   * @var array
   */
  protected $excluded_types = [
    'block_content',
    'comment',
    'contact_message',
    'file',
    'shortcut',
    'menu_link_content',
  ];

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $allEntityTypeBundleInfo;

  /**
   * Constructs a new EntityTypeMapper object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfo $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->allEntityTypeBundleInfo = $entity_type_bundle_info->getAllBundleInfo();
  }

  /**
   * Generate grouped options for entity type select form element
   * @return array
   *
   * @see \Drupal\ws_data_sync\Form\FeedForm
   */
  public function getContentEntityTypes() {
    $type_options = [];
    $entity_types = $this->entityTypeManager->getDefinitions();

    /**
     * @var string $id
     * @var \Drupal\Core\Entity\EntityTypeInterface $entity_type
     */
    foreach ($entity_types as $id => $entity_type) {
      if (self::typeIsSelectable($entity_type)) {
        $grouping_label = $entity_type->getLabel()->render();
        $grouped_options = self::getBundleOptions($id);
        $type_options[$grouping_label] = $grouped_options;
      }
    }

    return $type_options;
  }


  /**
   * Generate selectable options with option values formatted "type:bundle"
   *
   * @param string $type_id
   *
   * @return array
   */
  protected function getBundleOptions($type_id) {
    $bundles_options = [];
    foreach ($this->allEntityTypeBundleInfo[$type_id] as $bundle_id => $bundle_data) {
      $bundles_options[$type_id . ':' . $bundle_id] = $bundle_data['label'];
    }
    return $bundles_options;
  }


  /**
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *
   * @return boolean
   */
  protected function typeIsSelectable($entity_type) {
    if (!$entity_type instanceof ContentEntityType) {
      return FALSE;
    }
    if (in_array($entity_type->id(), $this->excluded_types)) {
      return FALSE;
    }
    if (!key_exists($entity_type->id(), $this->allEntityTypeBundleInfo)) {
      return FALSE;
    }

    return TRUE;
  }

}
