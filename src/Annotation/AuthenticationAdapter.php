<?php

namespace Drupal\ws_data_sync\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Authentication item annotation object.
 *
 * @see \Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager
 * @see plugin_api
 *
 * @Annotation
 */
class AuthenticationAdapter extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
