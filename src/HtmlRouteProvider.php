<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for all Webservice data sync entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class HtmlRouteProvider extends AdminHtmlRouteProvider {

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
    $route_collection->add('entity.feed.collection', $route);


    $route = (new Route('/admin/config/services/data-sync/{webservice}/{feed}/fieldmappings'))
      ->setDefaults([
        '_title' => 'Fieldmappings',
        '_entity_list' => 'fieldmapping'
      ])
      ->setRequirement('_permission', 'manage feeds');
    $route_collection->add('entity.fieldmapping.collection', $route);

//    $route = (new Route('/admin/config/services/data-sync/{webservice}/{feed}/fieldmapping/{field_mapping}/edit'))
//      ->setDefaults([
//        '_title' => 'Fieldmappings',
//        '_entity_list' => 'fieldmapping'
//      ])
//      ->setRequirement('_permission', 'manage feeds');
//    $route_collection->add('entity.fieldmapping.collection', $route);

    return $route_collection;
  }

}
