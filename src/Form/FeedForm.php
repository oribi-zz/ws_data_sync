<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FeedForm.
 */
class FeedForm extends EntityForm {

  use ComplexKeyFormatterTrait;

  public function buildForm(array $form, FormStateInterface $form_state, Request $request = null) {
    $form = parent::buildForm($form, $form_state);

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
      '#options' => ['' => t('- Select -')] + $type_options,
      '#default_value' => is_array($feed->getLocal()) ? self::toOption($feed->getLocal()) : '',
      '#required' => TRUE,
    ];

    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#default_value' => $feed->getEndpoint(),
      '#required' => TRUE,
    ];

    $form['webservice'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webservice'),
      '#default_value' => $request->get('webservice'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $feed = $this->entity;

    // Massage colon separated 'local' value to array for structured config storage
    $keys = self::getConfigPropertySequenceMappingKeys('local', $feed->getEntityTypeId());
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
    $webservice_feed_list = Url::fromRoute('entity.feed.collection', ['webservice' => $feed->getWebservice()]);
    $form_state->setRedirectUrl($webservice_feed_list);
  }

}
