<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\webprofiler\Entity\EntityManagerWrapper;
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
   * @var \Drupal\webprofiler\Entity\EntityManagerWrapper
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $allEntityTypeBundleInfo;

  /**
   * Constructs a new EntityTypeMapper object.
   */
  public function __construct(EntityManagerWrapper $entity_type_manager, EntityTypeBundleInfo $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->allEntityTypeBundleInfo = $entity_type_bundle_info->getAllBundleInfo();
  }

  /**
   * Generate grouped options for 'local' (entity type select) form element
   * @return array
   *
   * @see \Drupal\ws_data_sync\Form\FeedForm
   */
  public function getContentEntityTypes() {
    $type_options = [];
    $entity_types = $this->entityTypeManager->getDefinitions();

    foreach ($entity_types as $label => $definition) {
      if ($definition instanceof ContentEntityType && !in_array($label, $this->excluded_types)) {
        $type_options[$definition->getLabel()->render()] = self::getTypeBundles($label);
      }
    }

    return $type_options;
  }


  /**
   * Generate actual option values with format "type:bundle"
   * @param $type_label
   *
   * @return array
   */
  protected function getTypeBundles($type_label) {
    $type_bundles_options = [];
    $bundle_info = $this->allEntityTypeBundleInfo;
    foreach ($bundle_info[$type_label] as $key => $label) {
      $type_bundles_options[$type_label . ':' . $key] = $label['label'];
    }
    return $type_bundles_options;
  }

}
