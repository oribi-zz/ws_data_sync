<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FieldMappingForm.
 */
class FieldMappingForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = null) {
    $form = parent::buildForm($form, $form_state);

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

    $form['feed'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feed'),
      '#maxlength' => 255,
      '#default_value' => $request->get('feed'),
      '#required' => TRUE,
    ];

    $form['webservice'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webservice'),
      '#maxlength' => 255,
      '#default_value' => $request->get('webservice'),
      '#required' => TRUE,
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $field_mapping = $this->entity;
    $status = $field_mapping->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Field Mapping.', [
          '%label' => $field_mapping->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Field Mapping.', [
          '%label' => $field_mapping->label(),
        ]));
    }

    $feed_field_mapping_list = Url::fromRoute(
      'entity.field_mapping.collection', [
        'webservice' => $form_state->getValue('webservice'),
        'feed' => $form_state->getValue('feed')
      ]);
    $form_state->setRedirectUrl($feed_field_mapping_list);
  }

}
