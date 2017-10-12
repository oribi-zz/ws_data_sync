<?php

namespace Drupal\ws_data_sync\Controller;

use Drupal\Core\Entity\Controller\EntityController as CoreEntityController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class EntityController extends CoreEntityController {

  /**
   * @param \Drupal\ws_data_sync\Entity\WebserviceInterface $webservice
   * @param \Drupal\ws_data_sync\Entity\FeedInterface $feed
   *
   * @return string
   */
  public function collectionTitle($webservice = null, $feed = null) {
    if ($feed) {
      return t('@label field mappings', ['@label' => $feed->label()]);
    }

    if ($webservice) {
      return t('@label feeds', ['@label' => $webservice->label()]);
    }

    return t('Data sync webservices');

  }

  /**
   * @inheritDoc
   */
  public function addTitle($entity_type_id) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $params = \Drupal::request()->attributes;
    // Todo: find a better way to get parent entity
    if ($params->get('webservice') || $params->get('feed') ) {
      $parent = $params->get('feed') ? $params->get('feed') : $params->get('webservice');
      return $this->t('Add @parent @entity-type', [
        '@parent' => $parent->label(),
        '@entity-type' => $entity_type->getLowercaseLabel(),
      ]);
    } else {
      return $this->t('Add @entity-type', ['@entity-type' => $entity_type->getLowercaseLabel()]);
    }
  }


  /**
   * @inheritDoc
   */
  public function editTitle(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($entity = $this->doGetEntity($route_match, $_entity)) {
      return $this->t('Edit %label @entity-type', [
        '%label' => $entity->label(),
        '@entity-type' => $entity->getEntityType()->getLowercaseLabel()
      ]);
    }
    return parent::editTitle($route_match);
  }

  /**
   * @inheritDoc
   */
  protected function doGetEntity(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($_entity) {
      $entity = $_entity;
    }
    else {
      // Invert default iteration order to match this modules route structure
      // @see parent::doGetEntity
      foreach (array_reverse($route_match->getParameters()->all()) as $parameter) {
        if ($parameter instanceof EntityInterface) {
          $entity = $parameter;
          break;
        }
      }
    }
    if (isset($entity)) {
      return $this->entityRepository->getTranslationFromContext($entity);
    }
  }


}