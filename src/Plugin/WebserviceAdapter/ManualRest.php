<?php

namespace Drupal\ws_data_sync\Plugin\WebserviceAdapter;

use Drupal\ws_data_sync\Plugin\WebserviceAdapterBase;

/**
 * Provides a Manual REST webservice type.
 *
 * Based on the official OData specification
 *
 * @WebserviceAdapter(
 *   id = "manual_rest",
 *   name = "Manual REST"
 * )
 */
class ManualRest extends WebserviceAdapterBase {

  public function getEndpoints() {
    return ['test1' => 'Test 1', 'test2' => 'Test 2'];
  }

}