<?php

namespace Drupal\ws_data_sync\Plugin\AuthenticationAdapter;

use Drupal\ws_data_sync\Plugin\AuthenticationAdapterBase;

/**
 * Provides a Simple Auth authentication type.
 *
 * @AuthenticationAdapter(
 *   id = "test_auth",
 *   name = "Test Auth"
 * )
 */
class TestAuth extends AuthenticationAdapterBase {

  public function getCredentialParams($authentication) {
    $params = [
      't_user' => [
        '#type' => 'textfield',
        '#title' => 'Test User',
        '#length' => 64,
        '#default_value' => $authentication['credentials']['t_password'] ?? '',
      ],
      't_password' => [
        '#type' => 'textfield',
        '#title' => 'Test Password',
        '#length' => 64,
        '#default_value' => $authentication['credentials']['t_password'] ?? '',
      ],
    ];

    return $params;
  }
}