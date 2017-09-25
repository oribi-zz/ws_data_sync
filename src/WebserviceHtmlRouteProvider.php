<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Webservice entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class WebserviceHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $route_collection = parent::getRoutes($entity_type);

    $route = (new Route('/admin/config/services/data-sync/{webservice}/feeds'))
      ->setDefaults([
        '_title' => 'Feeds',
        '_entity_list' => 'feed'
      ])
      ->setRequirement('_permission', 'manage feeds');
    $route_collection->add('entity.webservice.manage_feeds', $route);

    return $route_collection;
  }

}
