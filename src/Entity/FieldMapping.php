<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Field Mapping entity.
 *
 * @ConfigEntityType(
 *   id = "field_mapping",
 *   label = @Translation("Field Mapping"),
 *   label_plural = @Translation("Field Mappings"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ws_data_sync\FieldMappingListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ws_data_sync\Form\FieldMappingForm",
 *       "edit" = "Drupal\ws_data_sync\Form\FieldMappingForm",
 *       "delete" = "Drupal\ws_data_sync\Form\FieldMappingDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ws_data_sync\HtmlRouteProvider",
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
 *     "add-form" = "/admin/config/services/data-sync/{webservice}/{feed}/field-mapping/add",
 *     "edit-form" = "/admin/config/services/data-sync/{webservice}/{feed}/{field_mapping}/edit",
 *     "delete-form" = "/admin/config/services/data-sync/{webservice}/{feed}/{field_mapping}/delete",
 *     "collection" = "/admin/config/services/data-sync/{webservice}/{feed}/field-mappings"
 *   },
 *   ancestors = {
 *     "webservice",
 *     "feed"
 *   }
 * )
 */
class FieldMapping extends ConfigEntityBase {

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
   * @return string
   */
  public function getWebservice(): string {
    return $this->webservice;
  }

  /**
   * @param string $webservice
   */
  public function setWebservice(string $webservice) {
    $this->webservice = $webservice;
  }

  /**
   * @param string $feed
   */
  public function setFeed(string $feed) {
    $this->feed = $feed;
  }

  /**
   * @return string
   */
  public function getFeed(): string {
    return $this->feed;
  }

  /**
   * @return mixed
   */
  public function getLocal() {
    return $this->local;
  }

  /**
   * @inheritDoc
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependency('config', 'ws_data_sync.webservice.' . $this->webservice);
    $this->addDependency('config', 'ws_data_sync.feed.' . $this->feed);
    return $this;
  }

}
