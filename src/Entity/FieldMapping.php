<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Field Mapping entity.
 *
 * @ConfigEntityType(
 *   id = "field_mapping",
 *   label = @Translation("Field Mapping"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ws_data_sync\FieldMappingListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ws_data_sync\Form\FieldMappingForm",
 *       "edit" = "Drupal\ws_data_sync\Form\FieldMappingForm",
 *       "delete" = "Drupal\ws_data_sync\Form\FieldMappingDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ws_data_sync\FieldMappingHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "field_mapping",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/data-sync/field-mapping/{webservice}/{feed}/{field_mapping}",
 *     "add-form" = "/admin/config/services/data-sync/{webservice}/{feed}/fieldmapping/add",
 *     "edit-form" = "/admin/config/services/data-sync/{webservice}/{feed}/{field_mapping}/edit",
 *     "delete-form" = "/admin/config/services/data-sync/{webservice}/{feed}/{field_mapping}/delete",
 *     "collection" = "/admin/config/services/data-sync/{webservice}/{feed}/fieldmappings"
 *   }
 * )
 */
class FieldMapping extends ConfigEntityBase implements FieldMappingInterface {

  /**
   * The Field Mapping ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Field Mapping label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Field Mapping label.
   *
   * @var string
   */
  protected $webservice;

  /**
   * The Field Mapping label.
   *
   * @var string
   */
  protected $feed;

  /**
   * @var
   */
  protected $local;

  /**
   * @return mixed
   */
  public function getLocal() {
    return $this->local;
  }

  /**
   * @return string
   */
  public function getWebservice(): string {
    return $this->webservice;
  }

  /**
   * @return string
   */
  public function getFeed(): string {
    return $this->feed;
  }



}
