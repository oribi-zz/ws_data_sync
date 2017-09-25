<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Feed entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class FeedHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

//    $route = (new Route('/feeeed/{feed}/delete'))
//      ->addDefaults([
//        '_entity_form' => 'feed.delete',
//        '_title' => 'Deletetetete',
//      ])
////      ->setRequirement('feed', '\d+')
//      ->setRequirement('_entity_access', 'node.view');
////      ->setOption('_node_operation_route', TRUE);
//    $collection->add('entity.feed.delete_form', $route);

    return $collection;
  }

}
