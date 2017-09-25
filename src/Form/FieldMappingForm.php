<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FieldMappingForm.
 */
class FieldMappingForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

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

    /* You will need additional form elements for your custom properties. */

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
    $form_state->setRedirectUrl($field_mapping->toUrl('collection'));
  }

}
