<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 12-09-2017
 * Time: 12:56
 */

namespace Drupal\ws_data_sync\Plugin\AuthenticationAdapter;


use Drupal\ws_data_sync\Plugin\AuthenticationAdapterBase;

/**
 * Provides a API key authentication type.
 *
 * @AuthenticationAdapter(
 *   id = "api_key",
 *   name = "API key"
 * )
 */
class ApiKey extends AuthenticationAdapterBase {

  public function getCredentialParams() {
    $params = [
      'key' => [
        '#type' => 'textfield',
        '#title' => 'API key',
        '#length' => 128,
        '#attributes' => ['name' => 'authentication[params][key]'],
      ],
    ];

    return $params;
  }
}