<?php

namespace Drupal\ws_data_sync\Plugin\WebserviceAdapter;

use Drupal\ws_data_sync\Plugin\WebserviceAdapterBase;

/**
 * Provides a SOAP webservice type.
 *
 * Based on the official OData specification
 *
 * @WebserviceAdapter(
 *   id = "soap",
 *   name = "SOAP"
 * )
 */
class Soap extends WebserviceAdapterBase {

  public function getEndpoints() {
    return ['odata1' => 'OData 1', 'odata2' => 'OData 2'];
  }

}