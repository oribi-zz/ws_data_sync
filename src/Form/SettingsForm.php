<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ws_data_sync\EntityTypeMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * @var \Drupal\ws_data_sync\EntityTypeMapper
   */
  private $entityTypeMapper;

  /**
   * @inheritDoc
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeMapper $entityTypeMapper) {
    parent::__construct($config_factory);
    $this->entityTypeMapper = $entityTypeMapper;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('ws_data_sync.entity_type_mapper')
    );
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'ws_data_sync_settings_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ws_data_sync.settings');

    $form['entity_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Entity settings'),
      '#open' => TRUE,
    ];

    $form['entity_settings']['non_mappable_entities'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Non-mappable entities'),
      '#options' => $this->entityTypeMapper->getContentEntityTypesConfigOptions(),
      '#default_value' => $config->get('non_mappable_entities') ?: [],
      '#required' => TRUE,
      '#description' => $this->t('Selected entity types are removed as options from drop-down in feed edit and create forms.'),
    ];

//    $form['field_settings'] = [
//      '#type' => 'details',
//      '#title' => $this->t('Field settings'),
//    ];
//
//    $form['field_settings']['mappable_fields'] = [
//      '#type' => 'checkboxes',
//      '#title' => $this->t('Mappable fields'),
//      '#options' => $this->entityTypeMapper->getContentEntityTypes(),
////      '#options' => [1,2,3],
////      '#options' => $this->getLinkLabels(),
//      '#default_value' => $config->get('mappable_fields') ?: [],
//      '#required' => TRUE,
//      '#description' => $this->t('Select field types available for mapping feed data to.'),
//    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * @inheritDoc
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $non_mappable_entities = array_keys(array_filter($values['non_mappable_entities']));
    $this->config('ws_data_sync.settings')
      ->set('non_mappable_entities', $non_mappable_entities)
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return [
      'ws_data_sync.settings',
      ];
  }

}