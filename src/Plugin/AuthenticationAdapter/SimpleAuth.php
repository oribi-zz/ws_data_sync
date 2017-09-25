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
 *   id = "simple_auth",
 *   name = "Simple Auth"
 * )
 */
class SimpleAuth extends AuthenticationAdapterBase {

  public function getCredentialParams() {
    $params = [
      's_user' => [
        '#type' => 'textfield',
        '#title' => 'Simple User',
        '#length' => 64,
        '#attributes' => ['name' => 'authentication[params][s_user]'],
      ],
      's_password' => [
        '#type' => 'textfield',
        '#title' => 'Simple Password',
        '#length' => 64,
        '#attributes' => ['name' => 'authentication[params][s_password]'],
      ],
    ];

    return $params;
  }

}