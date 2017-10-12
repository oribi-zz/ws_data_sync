<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Feed entity.
 *
 * @ConfigEntityType(
 *   id = "feed",
 *   label = @Translation("Feed"),
 *   label_plural = @Translation("Feeds"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ws_data_sync\FeedListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ws_data_sync\Form\FeedForm",
 *       "edit" = "Drupal\ws_data_sync\Form\FeedForm",
 *       "delete" = "Drupal\ws_data_sync\Form\FeedDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ws_data_sync\HtmlRouteProvider",
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
 *     "add-form" = "/admin/config/services/data-sync/{webservice}/feed/add",
 *     "edit-form" = "/admin/config/services/data-sync/{webservice}/{feed}/edit",
 *     "delete-form" = "/admin/config/services/data-sync/{webservice}/{feed}/delete",
 *     "collection" = "/admin/config/services/data-sync/{webservice}/feeds"
 *   },
 *   ancestors = {
 *     "webservice"
 *   }
 * )
 */
class Feed extends ConfigEntityBase {

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
   * @var string
   */
  protected $webservice;

  /**
   * The local entity type.
   *
   * @var array
   */
  protected $local;

  /**
   * @var string
   */
  protected $endpoint;

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
   * @return array|null
   */
  public function getLocal() {
    return $this->local;
  }

  /**
   * @return string
   */
  public function getEndpoint() {
    return $this->endpoint;
  }

  /**
   * @inheritDoc
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependency('config', 'ws_data_sync.webservice.' . $this->webservice);
    return $this;
  }

}
