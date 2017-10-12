<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for all Webservice data sync entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 */
class HtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * @inheritDoc
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    if ($route = parent::getEditFormRoute($entity_type)) {
      $route->setDefault('_title_callback', '\Drupal\ws_data_sync\Controller\EntityController::editTitle');
      return $route;
    }
  }

  /**
   * @inheritDoc
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type) {
    if ($route = parent::getAddFormRoute($entity_type)) {
      $route->setDefault('_title_callback', '\Drupal\ws_data_sync\Controller\EntityController::addTitle');
      return $route;
    }
  }

  /**
   * @inheritDoc
   */
  protected function getDeleteFormRoute(EntityTypeInterface $entity_type) {
    if ($route = parent::getDeleteFormRoute($entity_type)) {
      $route->setDefault('_title_callback', '\Drupal\ws_data_sync\Controller\EntityController::deleteTitle');
      return $route;
    }
  }

  /**
   * @inheritDoc
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass() && ($admin_permission = $entity_type->getAdminPermission())) {
      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route->addDefaults([
          '_entity_list' => $entity_type->id(),
          '_title_callback' => '\Drupal\ws_data_sync\Controller\EntityController::collectionTitle'
        ])
        ->setRequirement('_permission', $admin_permission);
      if (!empty($entity_type->get('ancestors'))) {
        foreach ($entity_type->get('ancestors') as $ancestor) {
          $parameters[$ancestor] = 'type:' . $ancestor;
        }
        $route->setOption('parameters', $parameters);
      }
      return $route;
    }

    return parent::getCollectionRoute($entity_type);
  }


}
