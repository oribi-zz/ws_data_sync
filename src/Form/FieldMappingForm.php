<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ws_data_sync\Entity\FeedInterface;
use Drupal\ws_data_sync\Entity\WebserviceInterface;
use Drupal\ws_data_sync\EntityFieldMapper;
use Drupal\ws_data_sync\EntityTypeMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FieldMappingForm.
 */
class FieldMappingForm extends EntityForm {

  /**
   * @var \Drupal\ws_data_sync\Entity\WebserviceInterface
   */
  private $webservice;

  /**
   * @var \Drupal\ws_data_sync\Entity\FeedInterface
   */
  private $feed;

  /**
   * @var \Drupal\ws_data_sync\EntityFieldMapper
   */
  private $entityFieldMapper;

  /**
   * @inheritDoc
   */
  public function __construct(EntityFieldMapper $entityFieldMapper) {
    $this->entityFieldMapper = $entityFieldMapper;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ws_data_sync.entity_field_mapper')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = null) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ws_data_sync\Entity\Feed $feed */
    $this->feed = $request->get('feed');

    /** @var \Drupal\ws_data_sync\Entity\Webservice $webservice */
    $this->webservice = $request->get('webservice');

    /** @var \Drupal\ws_data_sync\Entity\FieldMapping $field_mapping */
    $field_mapping = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $field_mapping->label(),
      '#description' => $this->t("Label for the Field Mapping."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $field_mapping->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ws_data_sync\Entity\FieldMapping::load',
      ],
      '#disabled' => !$field_mapping->isNew(),
    ];

    $entity_fields = $this->entityFieldMapper->getEntityFields(
      $this->feed->getLocal()['type'],
      $this->feed->getLocal()['bundle']
    );

    $form['local'] = [
      '#type' => 'select',
      '#title' => $this->t('Local entity field'),
      '#description' => $this->t('Which field should the remote data be mapped to'),
      '#options' => $entity_fields,
    ];

    // Todo: create common method for rewriting delete route
    $form['actions']['delete']['#url'] = Url::fromRoute('entity.field_mapping.delete_form', [
      'webservice' => $this->webservice->id(),
      'feed' => $this->feed->id(),
      'field_mapping' => $field_mapping->id(),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $field_mapping = $this->entity;

    if ($field_mapping->isNew()) {
      $field_mapping->setWebservice($this->webservice->id());
      $field_mapping->setFeed($this->feed->id());
    }
    $status = $field_mapping->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Field Mapping.', [
          '%label' => $field_mapping->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %webservice %label Field Mapping.', [
          '%label' => $field_mapping->label(),
        ]));
    }

    $feed_field_mapping_list = Url::fromRoute(
      'entity.field_mapping.collection', [
        'webservice' => $this->webservice->id(),
        'feed' => $this->feed->id()
      ]);
    $form_state->setRedirectUrl($feed_field_mapping_list);
  }

}