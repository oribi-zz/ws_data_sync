<?php

namespace Drupal\ws_data_sync\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Webservice entities.
 */
interface WebserviceInterface extends ConfigEntityInterface {

  public function ws_url();

  public function ws_type();

  public function ws_authentication();

}
