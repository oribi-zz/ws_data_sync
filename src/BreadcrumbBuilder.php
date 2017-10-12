<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\system\PathBasedBreadcrumbBuilder;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * Class BreadcrumbBuilder
 *
 * @package Drupal\ws_data_sync
 */
class BreadcrumbBuilder extends PathBasedBreadcrumbBuilder {

  /** @var \Drupal\ws_data_sync\Entity\WebserviceInterface */
  private $webservice;

  /** @var \Drupal\ws_data_sync\Entity\FeedInterface */
  private $feed;

  /** @var \Drupal\ws_data_sync\Entity\FieldMappingInterface */
  private $fieldMapping;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityManager;

  /**
   * BreadcrumbBuilder constructor.
   *
   * @param \Drupal\Core\Routing\RequestContext $context
   * @param \Drupal\Core\Access\AccessManagerInterface $access_manager
   * @param \Symfony\Component\Routing\Matcher\RequestMatcherInterface $router
   * @param \Drupal\Core\PathProcessor\InboundPathProcessorInterface $path_processor
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   * @param \Drupal\Core\Session\AccountInterface $current_user
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   */
  public function __construct(RequestContext $context, AccessManagerInterface $access_manager, RequestMatcherInterface $router, InboundPathProcessorInterface $path_processor, ConfigFactoryInterface $config_factory, TitleResolverInterface $title_resolver, AccountInterface $current_user, CurrentPathStack $current_path, EntityTypeManagerInterface $entity_manager) {
    parent::__construct($context, $access_manager, $router, $path_processor, $config_factory, $title_resolver, $current_user, $current_path);
    $this->webservice = $entity_manager->getStorage('webservice');
    $this->feed = $entity_manager->getStorage('feed');
    $this->fieldMapping = $entity_manager->getStorage('field_mapping');
    $this->entityManager = $entity_manager;
  }

  /**
   * @inheritDoc
   */
  public function applies(RouteMatchInterface $route_match) {
    return in_array($route_match->getRouteName(), $this->affectedRoutes($this->webservice->getEntityType()))
      || in_array($route_match->getRouteName(), $this->affectedRoutes($this->feed->getEntityType()))
      || in_array($route_match->getRouteName(), $this->affectedRoutes($this->fieldMapping->getEntityType()));
  }

  /**
   * @inheritDoc
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = parent::build($route_match);
    // Todo: Breadcrumb link text doesn't update after renaming config entities
    // @see https://www.drupal.org/node/2513570 (issue/bug)

    // Todo: Remove extra link to list page on webservice form routes
    if (in_array($route_match->getRouteName(), array_merge(
      $this->affectedRoutes($this->webservice->getEntityType(), FALSE),
      $this->affectedRoutes($this->feed->getEntityType()),
      $this->affectedRoutes($this->fieldMapping->getEntityType())
    ))) {
      $breadcrumb->addLink(Link::createFromRoute($this->t('Data sync'), 'entity.webservice.collection'));
    }

    if (in_array($route_match->getRouteName(), array_merge(
      $this->affectedRoutes($this->feed->getEntityType(), FALSE),
      $this->affectedRoutes($this->fieldMapping->getEntityType())
    ))) {
      $webservice = $route_match->getParameter('webservice');
      $breadcrumb->addLink(Link::createFromRoute(
        $this->t($webservice->label() . ' feeds'), 'entity.feed.collection', [
          'webservice' => $webservice->id(),
        ]
      ));
    }

    if (in_array($route_match->getRouteName(), $this->affectedRoutes($this->fieldMapping->getEntityType(), FALSE))) {
      $webservice = $route_match->getParameter('webservice');
      $feed = $route_match->getParameter('feed');
      $breadcrumb->addLink(Link::createFromRoute(
        $this->t($feed->label() . ' field mappings'), 'entity.field_mapping.collection', [
          'webservice' => $webservice->id(),
          'feed' => $feed->id(),
        ]
      ));
    }

    return $breadcrumb;
  }

  /**
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param bool $include_collection
   *
   * @return array
   */
  protected function affectedRoutes(EntityTypeInterface $entity_type, $include_collection = TRUE) {
    $route_names = [];
    $route_providers = $this->entityManager->getRouteProviders($entity_type->id());
    foreach ($route_providers as $provider) {
      $routes = $provider->getRoutes($entity_type)->all();
      if (!$include_collection) {
        unset($routes['entity.' . $entity_type->id() . '.collection']);
      }

      // Todo: Find out why feed collection route shows up in field mappings routes array
      // Workaround to remove field mapping breadcrumb from feeds collection page
      if ($entity_type->id() == 'field_mapping') {
        unset($routes['entity.feed.collection']);
      }
      $route_names += array_keys($routes);
    }
    return $route_names;
  }

}
