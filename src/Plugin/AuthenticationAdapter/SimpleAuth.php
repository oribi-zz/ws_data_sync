<?php

namespace Drupal\ws_data_sync\Plugin\AuthenticationAdapter;

use Drupal\ws_data_sync\Plugin\AuthenticationAdapterBase;

/**
 * Provides a Simple Auth authentication type.
 *
 * @AuthenticationAdapter(
 *   id = "simple_auth",
 *   name = "Simple Auth"
 * )
 */
class SimpleAuth extends AuthenticationAdapterBase {

  public function getCredentialParams($authentication) {
    $params = [
      's_user' => [
        '#type' => 'textfield',
        '#title' => 'Simple User',
        '#length' => 64,
        '#default_value' => $authentication['credentials']['s_user'] ?? '',
      ],
      's_password' => [
        '#type' => 'textfield',
        '#title' => 'Simple Password',
        '#length' => 64,
        '#default_value' => $authentication['credentials']['s_password'] ?? '',
      ],
    ];

    return $params;
  }

}