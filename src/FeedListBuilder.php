<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of Feed entities.
 */
class FeedListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Feed');
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
    $operations = parent::getOperations($entity);

    $request = \Drupal::request();

    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.feed.edit_form', [
        'webservice' => $request->get('webservice'),
        'feed' => $entity->id()
      ]),
    ];

    $operations['manage_field_mappings'] = [
      'title' => t('Manage field mappings'),
      'weight' => -100,
      'url' => Url::fromRoute('entity.field_mapping.collection', ['webservice' => $request->get('webservice'), 'feed' => $entity->id()]),
    ];



    unset($operations['translate']);

    return $operations;

  }


}
