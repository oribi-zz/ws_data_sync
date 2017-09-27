<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of Webservice entities.
 */
class WebserviceListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Webservice');
//    $header['id'] = $this->t('Machine name');
    $header['type'] = $this->t('Type');
    $header['authentication'] = $this->t('Authentication');
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
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

  public function getOperations(EntityInterface $entity) {
    $operations =  parent::getOperations($entity);
    unset($operations['translate']);

    $feed_count = \Drupal::entityQuery('feed')->condition('webservice', $entity->id())->count()->execute();
    if ($feed_count > 0) {
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
