<?php

namespace Drupal\ws_data_sync\Plugin\AuthenticationAdapter;

use Drupal\ws_data_sync\Plugin\AuthenticationAdapterBase;

/**
 * Provides a Json Web Token authentication type.
 *
 * @AuthenticationAdapter(
 *   id = "jwt",
 *   name = "Json Web Token"
 * )
 */
class JsonWebToken extends AuthenticationAdapterBase {

  public function getCredentialParams($authentication) {
    $params = [
      'placeholder' => [
        '#type' => 'textfield',
        '#title' => 'Placeholder',
        '#length' => 128,
        '#default_value' => $authentication['credentials']['placeholder'] ?? '',
      ],
    ];

    return $params;
  }
}