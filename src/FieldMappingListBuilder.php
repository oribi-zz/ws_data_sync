<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides a listing of Field Mapping entities.
 */
class FieldMappingListBuilder extends ConfigEntityListBuilder {

  /**
   * @var \Drupal\ws_data_sync\Entity\WebserviceInterface
   */
  private $webservice;

  /**
   * @var \Drupal\ws_data_sync\Entity\FeedInterface
   */
  private $feed;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    parent::__construct($entity_type, $storage);
    $request = \Drupal::request();
    $this->webservice = $request->get('webservice');
    $this->feed = $request->get('feed');
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Field Mapping');
    $header['id'] = $this->t('Machine name');
    $header['webservice'] = $this->t('Webservice');
    $header['feed'] = $this->t('Feed');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['webservice'] = $entity->getWebservice();
    $row['feed'] = $entity->getFeed();

    return $row + parent::buildRow($entity);
  }

  /**
   * @inheritDoc
   */
  public function load() {
    return $this->getStorage()->loadByProperties([
      'webservice' => $this->webservice->id(),
      'feed' => $this->feed->id()
    ]);
  }


  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.field_mapping.edit_form', [
        'webservice' => $this->webservice->id(),
        'feed' => $this->feed->id(),
        'field_mapping' => $entity->id(),
      ]),
    ];

    $operations['delete'] = [
      'title' => $this->t('Delete'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.field_mapping.delete_form', [
        'webservice' => $this->webservice->id(),
        'feed' => $this->feed->id(),
        'field_mapping' => $entity->id(),
      ]),
    ];

    unset($operations['translate']);

    return $operations;

  }


}
