<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\ws_data_sync\Form\ComplexKeyFormatterTrait;

/**
 * Defines the Feed entity.
 *
 * @ConfigEntityType(
 *   id = "feed",
 *   label = @Translation("Feed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ws_data_sync\FeedListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ws_data_sync\Form\FeedForm",
 *       "edit" = "Drupal\ws_data_sync\Form\FeedForm",
 *       "delete" = "Drupal\ws_data_sync\Form\FeedDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ws_data_sync\FeedHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "feed",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/data-sync/feed/{feed}",
 *     "add-form" = "/admin/config/services/data-sync/feed/add",
 *     "edit-form" = "/admin/config/services/data-sync/feed/{feed}/edit",
 *     "delete-form" = "/admin/config/services/data-sync/feed/{feed}/delete",
 *     "collection" = "/admin/config/services/data-sync/feed"
 *   }
 * )
 */
class Feed extends ConfigEntityBase implements FeedInterface {

  protected $schemaId = 'ws_data_sync.feed.*';

  /**
   * The Feed ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Feed label.
   *
   * @var string
   */
  protected $label;

  /**
   * The local entity type.
   *
   * @var array
   */
  protected $local;

  /**
   * @var string
   */
  protected $webservice;

  /**
   * @return string
   */
  public function getSchemaId(): string {
    return $this->schemaId;
  }

  /**
   * @return array
   */
  public function getLocal(): array {
    return $this->local;
  }

  /**
   * @return string
   */
  public function getWebservice(): string {
    return $this->webservice;
  }



}
