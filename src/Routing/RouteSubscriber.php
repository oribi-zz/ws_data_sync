<?php

namespace Drupal\ws_data_sync\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    $field_mapping_form_parameters = [
      'webservice' => ['type' => 'entity:webservice'],
      'feed' => ['type' => 'entity:feed'],
    ];

    foreach (['entity.field_mapping.add_form', 'entity.field_mapping.edit_form'] as $route_name) {
      $route = $collection->get($route_name);
      $route->setOption('parameters', $field_mapping_form_parameters);
      $collection->add($route_name, $route);
    }

  }
}
