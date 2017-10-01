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
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage) {
    parent::__construct($entity_type, $storage);
    $this->request = \Drupal::request();
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

    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

  /**
   * @inheritDoc
   */
  public function load() {
    return $this->getStorage()->loadByProperties([
      'webservice' => $this->request->get('webservice'),
      'feed' => $this->request->get('feed')
    ]);
  }


  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.field_mapping.edit_form', [
        'webservice' => $this->request->get('webservice'),
        'feed' => $this->request->get('feed'),
        'field_mapping' => $entity->id(),
      ]),
    ];

    $operations['delete'] = [
      'title' => $this->t('Delete'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.field_mapping.delete_form', [
        'webservice' => $this->request->get('webservice'),
        'feed' => $this->request->get('feed'),
        'field_mapping' => $entity->id(),
      ]),
    ];

    unset($operations['translate']);

    return $operations;

  }


}
