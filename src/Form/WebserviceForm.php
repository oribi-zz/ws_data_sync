<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager;
use Drupal\ws_data_sync\Plugin\ResponseFormatAdapterManager;
use Drupal\ws_data_sync\Plugin\WebserviceAdapterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WebserviceForm.
 */
class WebserviceForm extends EntityForm {

  /**
   * @var \Drupal\ws_data_sync\Plugin\WebserviceAdapterManager
   */
  private $webserviceAdapter;

  /**
   * @var \Drupal\ws_data_sync\Plugin\ResponseFormatAdapterManager
   */
  private $formatAdapter;

  /**
   * @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager
   */
  private $authenticationAdapter;

  // Todo: Figure out why dependency injection fails when submitting the form via ajax...
//  public function __construct(WebserviceAdapterManager $webservice_adapter, ResponseFormatAdapterManager $format_adapter, AuthenticationAdapterManager $authentication_adapter) {
//    $this->webserviceAdapter = $webservice_adapter;
//    $this->formatAdapter = $format_adapter;
//    $this->authenticationAdapter = $authentication_adapter;
//  }


//  public static function create(ContainerInterface $container) {
//    return new static(
//      $container->get('plugin.manager.ws_data_sync.ws_adapter'),
//      $container->get('plugin.manager.ws_data_sync.response_format_adapter'),
//      $container->get('plugin.manager.ws_data_sync.authentication')
//    );
//  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ws_data_sync\Plugin\WebserviceAdapterManager $webservice_adapter */
    $this->webserviceAdapter = \Drupal::service('plugin.manager.ws_data_sync.ws_adapter');

    /** @var \Drupal\ws_data_sync\Plugin\ResponseFormatAdapterManager $format_adapter */
    $this->formatAdapter = \Drupal::service('plugin.manager.ws_data_sync.response_format_adapter');

    /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager $authentication_adapter */
    $this->authenticationAdapter = \Drupal::service('plugin.manager.ws_data_sync.authentication');

    /** @var \Drupal\ws_data_sync\Entity\Webservice $webservice */
    $webservice = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $webservice->label(),
      '#description' => $this->t("Label for the Webservice."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $webservice->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ws_data_sync\Entity\Webservice::load',
      ],
      '#disabled' => !$webservice->isNew(),
    ];

    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Url'),
      '#maxlength' => 255,
      '#default_value' => $webservice->ws_url(),
      '#description' => $this->t("Url for the Webservice."),
      '#required' => TRUE,
    ];

    $authentication = '';//$webservice->ws_authentication();
    $form['authentication'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    $authentication = $webservice->ws_authentication();

    // Todo: Load fields dynamically
    $form['authentication']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Authentication'),
      '#options' => $this->authenticationAdapter->getPluginsSelectList(),
//      '#options' => $this->authenticationAdapter->getPluginsSelectList(),
      '#default_value' => isset($authentication['type']) ? $authentication['type'] : '',
//      '#default_value' => $authentication['type'],
      '#empty_option' => t('- none -'),
      '#description' => $this->t("Save after change type to load fields."),
      '#required' => FALSE,
//      '#ajax' => [
//        'trigger_as' => ['name' => 'load_credentials'],
////        'wrapper' => 'webservice-edit-form',
//      ],
    ];

    if (isset($authentication['type']) && $authentication['type'] !== '') {
//    if ($authentication['type'] !== '' || FALSE) {
      /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterInterface $authentication_plugin */
      $authentication_plugin = $this->authenticationAdapter->createInstance($authentication['type']);
//      $authentication_plugin = $this->authenticationAdapter->createInstance($authentication['type']);
      $credentials = $authentication_plugin->getCredentialParams($authentication);
      $form['authentication']['credentials'] = [
        '#title' => t('Authentication credentials'),
        '#type' => 'details',
        '#open' => TRUE,
        '#states' => [
          'visible' => ['[name="authentication[type]"' => ['value' => $authentication['type']]],
        ]
      ] + $credentials;

    }

    $form['test_connection'] = [
      '#type' => 'button',
      '#value' => t('Test connection'),
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => $this->webserviceAdapter->getPluginsSelectList(),
//      '#options' => $this->webserviceAdapter->getPluginsSelectList(),
      '#default_value' => $webservice->ws_type(),
      '#description' => $this->t("Type of webservice."),
      '#required' => TRUE,
    ];

    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Remote format'),
      '#options' => $this->formatAdapter->getPluginsSelectList(),
//      '#options' => $this->formatAdapter->getPluginsSelectList(),
      '#default_value' => $webservice->getFormat(),
//      '#empty_option'  => t('- select -'),
      '#description' => $this->t("Format of the remote data."),
      '#required' => TRUE,
    ];

    $form['documentation_link'] = [
      '#type' => 'url',
      '#title' => $this->t('Documentation'),
      '#maxlength' => 255,
      '#default_value' => $webservice->getDocumentationLink(),
      '#description' => $this->t("Link to webservice documentation (optional)"),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
//    parent::submitForm($form, $form_state);

    dpm($this->entity->ws_authentication()['type'], 'entity');
    dpm($form_state->getValue('authentication')['type'], 'form state');

    if ($form_state->getValue('authentication')['type'] !== $this->entity->ws_authentication()['type']) {
      $form_state->setRebuild();
      $form_state->disableRedirect();
    }

  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $webservice = $this->entity;
    $status = $webservice->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Webservice.', [
          '%label' => $webservice->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Webservice.', [
          '%label' => $webservice->label(),
        ]));
    }

//    foreach ($form_state->getValues() as $key => $value) {
//      if (is_array($value)) {
//        drupal_set_message(t($key . ":<br>"));
//        foreach ($value as $inner_key => $inner_value) {
//          if (is_array($inner_value)) {
//            drupal_set_message(t("&nbsp;&nbsp;" . $inner_key . ":<br>"));
//            foreach ($inner_value as $inner_key2 => $inner_value2) {
//              drupal_set_message(t("&nbsp;&nbsp;&nbsp;&nbsp;" .$inner_key2 . ': ' . $inner_value2));
//            }
//          } else {
//            drupal_set_message(t("&nbsp;&nbsp;" . $inner_key . ': ' . $inner_value));
//          }
//        }
//      } else {
//        drupal_set_message($key . ': ' . $value);
//      }
//    }

    $form_state->setRedirectUrl($webservice->toUrl('collection'));
  }

}
