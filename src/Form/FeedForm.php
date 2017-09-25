<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FeedForm.
 */
class FeedForm extends EntityForm {

  use ComplexKeyFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

//    dsm($this->entity);
//    dsm($this->getEntity()->getEntityType());
//    dsm($this->getEntity()->getEntityType()->getKeys());

    /** @var \Drupal\ws_data_sync\EntityTypeMapper $entity_type_map */
    $entity_type_map = \Drupal::service('ws_data_sync.entity_type_mapper');

    $type_options = $entity_type_map->getContentEntityTypes();

    /** @var \Drupal\ws_data_sync\Entity\feed $feed */
    $feed = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $feed->label(),
      '#description' => $this->t("Label for the Feed."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $feed->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ws_data_sync\Entity\Feed::load',
      ],
      '#disabled' => !$feed->isNew(),
    ];

    $form['local'] = [
      '#type' => 'select',
      '#title' => $this->t('Local entity'),
      '#options' => $type_options,
      '#default_value' => self::toOption($feed->getLocal()),
      '#required' => TRUE,
    ];

    $form['webservice'] = [
      '#type' => 'select',
      '#title' => $this->t('Webservice'),
      '#options' => $type_options,
      '#default_value' => $feed->getWebservice(),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $feed = $this->entity;
//    dsm($this->entity->getTypedData());
    // Massage colon separated 'local' value to array for structured config storage
    $keys = self::getConfigPropertySequenceMappingKeys('local', $feed);
//    $keys = self::getConfigPropertySequenceMappingKeys('local');
    if (count($keys) == count(explode(':', $form_state->getValue('local')))) {
      $local = self::toArray($form_state->getValue('local'), $keys);
      $status = $feed->set('local', $local)->save();
    } else {
      drupal_set_message($this->t('Unmatched parameters'), 'error');
    }

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Feed.', [
          '%label' => $feed->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Feed.', [
          '%label' => $feed->label(),
        ]));
    }
    $form_state->setRedirectUrl($feed->toUrl('collection'));
  }

//  protected function getConfigPropertySequenceMappingKeys($property) {
//    $entity_schema_id = $this->getEntity()->getSchemaIdentifier();
//    $entity_definition = Drupal::service('config.typed')->getDefinition($entity_schema_id);
//    return array_keys($entity_definition['mapping'][$property]['sequence']['mapping']);
//  }

}
