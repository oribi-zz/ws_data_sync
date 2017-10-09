<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ws_data_sync\EntityDependants;
use Drupal\ws_data_sync\EntityTypeMapper;
use Drupal\ws_data_sync\Plugin\WebserviceAdapterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FeedForm.
 */
class FeedForm extends EntityForm {

  use ComplexKeyFormatterTrait;

  /**
   * @var \Drupal\ws_data_sync\EntityTypeMapper
   */
  private $entityTypeMapper;

  /**
   * @var \Drupal\ws_data_sync\Plugin\WebserviceAdapterManager
   */
  private $webserviceAdapterManager;

  /** @var  \Drupal\ws_data_sync\Entity\WebserviceInterface */
  private $webservice;

  /**
   * @var \Drupal\ws_data_sync\EntityDependants
   */
  private $dependants;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeMapper $entityTypeMapper, WebserviceAdapterManager $webserviceAdapterManager, EntityDependants $dependants) {
    $this->entityTypeMapper = $entityTypeMapper;
    $this->webserviceAdapterManager = $webserviceAdapterManager;
    $this->dependants = $dependants;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ws_data_sync.entity_type_mapper'),
      $container->get('plugin.manager.ws_data_sync.ws_adapter'),
      $container->get('ws_data_sync.entity_dependants')
    );
  }


  public function buildForm(array $form, FormStateInterface $form_state, Request $request = null, WebserviceAdapterManager $webserviceAdapterManager = null) {
    $form = parent::buildForm($form, $form_state);

    $type_options = $this->entityTypeMapper->getContentEntityTypes();

    /** @var \Drupal\ws_data_sync\Entity\Webservice $webservice */
    $this->webservice = $request->get('webservice');


    /** @var \Drupal\ws_data_sync\Entity\Feed $feed */
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
      '#description' => t('Which <em>drupal entity</em> should this feed be mapped to?'),
    ];

    /** @var \Drupal\ws_data_sync\Plugin\WebserviceAdapter\SpaceX $webservice_type_plugin */
    $webservice_type_plugin = $this->webserviceAdapterManager->createInstance($this->webservice->ws_type());

    $form['endpoint'] = [
      '#type' => 'select',
      '#title' => $this->t('Endpoint'),
      '#options' => $webservice_type_plugin->getEndpoints(),
      '#default_value' => $feed->getEndpoint(),
      '#required' => TRUE,
      '#description' => t('Which <em>remote resource</em> should this feed be mapped to?'),
    ];

    $dependant_params = ['webservice' => $this->webservice->id(), 'feed' => $feed->id()];
    if ($this->dependants->hasDependants('field_mapping',$dependant_params)) {
      $this->dependants->disableSourceFields($form,'field_mapping', $dependant_params, ['local', 'endpoint']);
    }

    // Todo: create common method for rewriting delete route
    $form['actions']['delete']['#url'] = Url::fromRoute('entity.feed.delete_form', [
      'webservice' => $this->webservice->id(),
      'feed' => $feed->id(),
    ]);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $feed = $this->entity;

    if ($feed->isNew()) {
      $feed->setWebservice($this->webservice);
    }

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
    $webservice_feed_list = Url::fromRoute('entity.feed.collection', ['webservice' => $this->webservice->id()]);
    $form_state->setRedirectUrl($webservice_feed_list);
  }

}
