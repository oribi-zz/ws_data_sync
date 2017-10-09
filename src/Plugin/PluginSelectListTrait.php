<?php

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