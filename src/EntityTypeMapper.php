<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfo;

/**
 * Class EntityTypeMapper.
 */
class EntityTypeMapper {

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfo
   */
  protected $allEntityTypeBundleInfo;

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * @var \Drupal\Core\Entity\EntityTypeInterface[]
   */
  private $entityTypes;

  /**
   * Constructs a new EntityTypeMapper object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfo $entity_type_bundle_info) {
    $this->config = $config_factory->get('ws_data_sync.settings');
    $this->entityTypes = $entity_type_manager->getDefinitions();
    $this->allEntityTypeBundleInfo = $entity_type_bundle_info->getAllBundleInfo();
  }

  /**
   * Generate entity-type-grouped bundle list
   * @return array
   *
   * @see \Drupal\ws_data_sync\Form\FeedForm
   */
  public function getContentEntityTypeBundles() {
    $type_options = [];

    /**
     * @var string $id
     * @var \Drupal\Core\Entity\EntityTypeInterface $entity_type
     */
    foreach ($this->entityTypes as $id => $entity_type) {
      if (self::typeIsSelectable($entity_type)) {
        $grouping_label = $entity_type->getLabel()->render();
        $grouped_options = self::getBundleOptions($id);
        $type_options[$grouping_label] = $grouped_options;
      }
    }

    return $type_options;
  }

  /**
   * Generate entity type list for module configuration
   * @return array
   *
   * @see \Drupal\ws_data_sync\Form\FeedForm
   */
  public function getContentEntityTypesConfigOptions() {
    $type_options = [];

    /**
     * @var string $id
     * @var \Drupal\Core\Entity\EntityTypeInterface $entity_type
     */
    foreach ($this->entityTypes as $id => $entity_type) {
      if ($entity_type instanceof ContentEntityType) {
        $type_options[$id] = $entity_type->getLabel()->render();
      }
    }
    asort($type_options);

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
   * Exclude config and user defined entities from entity mapping options
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *
   * @return boolean
   */
  protected function typeIsSelectable($entity_type) {
    if (!$entity_type instanceof ContentEntityType) {
      return FALSE;
    }
    // Exclude entity types disabled through configuration
    if (in_array($entity_type->id(), $this->config->get('non_mappable_entities'))) {
      return FALSE;
    }
    // Exclude types with no bundles
    if (!key_exists($entity_type->id(), $this->allEntityTypeBundleInfo)) {
      return FALSE;
    }

    return TRUE;
  }

}
