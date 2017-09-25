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
 * Provides a Simple Auth authentication type.
 *
 * @AuthenticationAdapter(
 *   id = "test_auth",
 *   name = "Test Auth"
 * )
 */
class TestAuth extends AuthenticationAdapterBase {

  public function getCredentialParams() {
    $params = [
      't_user' => [
        '#type' => 'textfield',
        '#title' => 'Test User',
        '#length' => 64,
        '#attributes' => ['name' => 'authentication[params][t_user]'],
      ],
      't_password' => [
        '#type' => 'textfield',
        '#title' => 'Test Password',
        '#length' => 64,
        '#attributes' => ['name' => 'authentication[params][t_password]'],
      ],
    ];

    return $params;
  }
}