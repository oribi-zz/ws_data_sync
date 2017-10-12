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
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $parameters = [];
    foreach (['feed', 'field_mapping'] as $entity_type) {
      foreach ($entity_type_manager->getDefinition($entity_type)->get('ancestors') as $ancestor) {
        $parameters[$ancestor] = ['type' => 'entity:'. $ancestor];
      }

      // Todo: find way to generate entity route array dynamically
      foreach (['entity.'. $entity_type . '.collection', 'entity.'. $entity_type .'.add_form', 'entity.'. $entity_type .'.edit_form', 'entity.'. $entity_type .'.delete_form'] as $route_name) {
        $route = $collection->get($route_name);
        $route->setOption('parameters', $parameters);
        $collection->add($route_name, $route);
      }
    }
  }
}
