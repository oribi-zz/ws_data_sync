<?php

namespace Drupal\ws_data_sync\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Response Format Adapter plugin manager.
 */
class ResponseFormatAdapterManager extends DefaultPluginManager {

  use PluginSelectListTrait;

  /**
   * Constructs a new ResponseFormatAdapterManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ResponseFormatAdapter', $namespaces, $module_handler, 'Drupal\ws_data_sync\Plugin\ResponseFormatAdapterInterface', 'Drupal\ws_data_sync\Annotation\ResponseFormatAdapter');

    $this->alterInfo('ws_data_sync_ws_data_sync.response_format_adapter_info');
    $this->setCacheBackend($cache_backend, 'ws_data_sync_ws_data_sync.response_format_adapter_plugins');
  }

}
