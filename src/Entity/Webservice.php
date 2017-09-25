<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Webservice entity.
 *
 * @ConfigEntityType(
 *   id = "webservice",
 *   label = @Translation("Webservice"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ws_data_sync\WebserviceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ws_data_sync\Form\WebserviceForm",
 *       "edit" = "Drupal\ws_data_sync\Form\WebserviceForm",
 *       "delete" = "Drupal\ws_data_sync\Form\WebserviceDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ws_data_sync\WebserviceHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "webservice",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/data-sync/webservice/{webservice}",
 *     "add-form" = "/admin/config/services/data-sync/webservice/add",
 *     "edit-form" = "/admin/config/services/data-sync/webservice/{webservice}/edit",
 *     "delete-form" = "/admin/config/services/data-sync/webservice/{webservice}/delete",
 *     "collection" = "/admin/config/services/data-sync/webservice",
 *     "manage-feeds" = "/admin/config/services/data-sync/{webservice}/feeds"
 *   }
 * )
 */
class Webservice extends ConfigEntityBase implements WebserviceInterface {

  protected $schemaId = 'ws_data_sync.webservice.*';

  /**
   * The Webservice ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Webservice label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Webservice type.
   *
   * @var string
   */
  protected $type;

  protected $format;

  /**
   * @return mixed
   */
  public function getFormat() {
    return $this->format;
  }

  /**
   * The Webservice url.
   *
   * @var string
   */
  protected $url;

  /**
   * The Webservice authentication.
   *
   * @var array
   */
  protected $authentication;

  /**
   * @var array
   */
//  protected $params;

  /**
   * @return string
   */
  public function ws_type() {
    return $this->type;
  }

  /**
   * @return string
   */
  public function ws_url() {
    return $this->url;
  }

  /**
   * @return array
   */
  public function ws_authentication() {
    return $this->authentication;
  }


  public function getSchemaIdentifier() {
    return $this->schemaId;
  }


}
