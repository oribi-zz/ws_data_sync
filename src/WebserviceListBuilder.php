<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Webservice entities.
 */
class WebserviceListBuilder extends ConfigEntityListBuilder {

  /**
   * @var \Drupal\ws_data_sync\EntityDependants
   */
  private $dependants;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, EntityDependants $dependants) {
    parent::__construct($entity_type, $storage);
    $this->dependants = $dependants;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('ws_data_sync.entity_dependants')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Webservice');
//    $header['id'] = $this->t('Machine name');
    $header['type'] = $this->t('Type');
    $header['authentication'] = $this->t('Authentication');
    $header['feeds'] = $this->t('Feeds');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    $row['label'] = $entity->label();
//    $row['id'] = $entity->id();
    $row['type'] = $entity->ws_type();
    $row['authentication'] = $entity->ws_authentication()['type'] ?: 'none';
    $row['feeds'] = $this->dependants->count('feed', ['webservice' => $entity->id()]);
    return $row + parent::buildRow($entity);
  }

  public function getOperations(EntityInterface $entity) {
    $operations =  parent::getOperations($entity);

    unset($operations['translate']);

    if ($this->dependants->hasDependants('feed', ['webservice' => $entity->id()])) {
      $operations['manage_feeds'] = [
        'title' => t('Manage feeds'),
        'weight' => 0,
        'url' => Url::fromRoute('entity.feed.collection', [
          'webservice' => $entity->id()
        ]),
      ];
    } else {
      $operations['add_feed'] = [
        'title' => t('Add feed'),
        'weight' => 0,
        'url' => Url::fromRoute('entity.feed.add_form', [
          'webservice' => $entity->id()
        ]),
      ];
    }
    uasort($operations, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $operations;
  }


}
