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

    $entity_form_route_parameters = [
      'webservice' => ['type' => 'entity:webservice'],
      'feed' => ['type' => 'entity:feed'],
      'field_mapping' => ['type' => 'entity:field_mapping'],
    ];

    foreach (['entity.feed.add_form', 'entity.feed.edit_form', 'entity.feed.delete_form'] as $route_name) {
      $route = $collection->get($route_name);
//      $route->setOption('parameters', [
//        'webservice' => $entity_form_route_parameters['webservice'],
//        'feed' => $entity_form_route_parameters['feed'],
//      ]);
      $route->setOption('parameters', $entity_form_route_parameters);
      $collection->add($route_name, $route);
    }

    foreach (['entity.field_mapping.add_form', 'entity.field_mapping.edit_form', 'entity.field_mapping.delete_form'] as $route_name) {
      $route = $collection->get($route_name);
      $route->setOption('parameters', $entity_form_route_parameters);
      $collection->add($route_name, $route);
    }

  }
}
