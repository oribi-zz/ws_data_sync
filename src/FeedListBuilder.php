<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Feed entities.
 */
class FeedListBuilder extends ConfigEntityListBuilder {

  /**
   * @var \Drupal\ws_data_sync\Entity\WebserviceInterface
   */
  private $webservice;

  /**
   * @var \Drupal\ws_data_sync\EntityDependants
   */
  private $dependants;

  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, EntityDependants $dependants) {
    parent::__construct($entity_type, $storage);
    $this->webservice = \Drupal::request()->get('webservice');
    $this->dependants = $dependants;
  }

  /**
   * @inheritDoc
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
    $header['label'] = $this->t('Feed');
//    $header['id'] = $this->t('Machine name');
    $header['webservice'] = $this->t('Webservice');
    $header['endpoint'] = $this->t('Endpoint');
    $header['field_mappings'] = $this->t('Field mappings');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    $row['label'] = $entity->label();
//    $row['id'] = $entity->id();
    $row['webservice'] = $entity->getWebservice();
    $row['endpoint'] = $entity->getEndpoint();
    $row['field_mappings'] = $this->dependants->count(
      'field_mapping', [
        'webservice' => $entity->getWebservice(),
        'feed' => $entity->id()
      ]
    );
    return $row + parent::buildRow($entity);
  }

  public function load() {
    return $this->getStorage()->loadByProperties([
      'webservice' => $this->webservice->id()
    ]);
  }

  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    $webservice = $entity->getWebservice();

    $operations['edit'] = [
      'title' => $this->t('Edit'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.feed.edit_form', [
        'webservice' => $webservice,
        'feed' => $entity->id()
      ]),
    ];

    $operations['delete'] = [
      'title' => $this->t('Delete'),
      'weight' => 10,
      'url' => Url::fromRoute('entity.feed.delete_form', [
        'webservice' => $webservice,
        'feed' => $entity->id()
      ]),
    ];

    unset($operations['translate']);

    if ($this->dependants->hasDependants('field_mapping', [
      'webservice' => $webservice,
      'feed' => $entity->id()
    ])) {
      $operations['manage_field_mappings'] = [
        'title' => t('Manage field mappings'),
        'weight' => 0,
        'url' => Url::fromRoute('entity.field_mapping.collection', [
          'webservice' => $webservice,
          'feed' => $entity->id()
        ]),
      ];
    } else {
      $operations['add_field_mappings'] = [
        'title' => t('Add field mapping'),
        'weight' => 0,
        'url' => Url::fromRoute('entity.field_mapping.add_form', [
          'webservice' => $webservice,
          'feed' => $entity->id(),
        ]),
      ];
    }
    uasort($operations, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $operations;
  }

}
