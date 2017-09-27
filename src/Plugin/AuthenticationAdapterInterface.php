<?php

namespace Drupal\ws_data_sync\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Authentication plugins.
 */
interface AuthenticationAdapterInterface extends PluginInspectionInterface {

  public function getCredentialParams($authentication);

}
