<?php

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

  public function getCredentialParams($authentication) {
    $params = [
      'key' => [
        '#type' => 'textfield',
        '#title' => 'API key',
        '#length' => 128,
        '#default_value' => $authentication['credentials']['key'] ?? '',
      ],
    ];

    return $params;
  }
}