<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 20-09-2017
 * Time: 10:48
 */

namespace Drupal\ws_data_sync\Plugin;


trait PluginSelectListTrait {


  /**
   * @return array
   */
  public function getPluginsSelectList() {
    $plugins = $this->getDefinitions();

    $select_list_options = [];
    foreach ($plugins as $id => $plugin) {
      $select_list_options[$id] = $plugin['name'];
    }
    return $select_list_options;
  }


}