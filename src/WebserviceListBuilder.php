<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Webservice entities.
 */
class WebserviceListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Webservice');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

  public function getOperations(EntityInterface $entity) {
    ksm(parent::getOperations($entity));
    $operations =  parent::getOperations($entity);
    $operations['manage_feeds'] = [
      'title' => t('Manage feeds'),
      'weight' => -10,
      'url' => $entity->toUrl('manage-feeds'),
    ];

    return $operations;
  }


}
