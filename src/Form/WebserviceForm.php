<?php

namespace Drupal\ws_data_sync\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ws_data_sync\Entity\WebserviceInterface;
use Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager;
use Drupal\ws_data_sync\Plugin\ResponseFormatAdapterManager;
use Drupal\ws_data_sync\Plugin\WebserviceAdapterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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

  public function __construct(WebserviceAdapterManager $webservice_adapter, ResponseFormatAdapterManager $format_adapter, AuthenticationAdapterManager $authentication_adapter) {
    $this->webserviceAdapter = $webservice_adapter;
    $this->formatAdapter = $format_adapter;
    $this->authenticationAdapter = $authentication_adapter;
  }


  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.ws_data_sync.ws_adapter'),
      $container->get('plugin.manager.ws_data_sync.response_format_adapter'),
      $container->get('plugin.manager.ws_data_sync.authentication')
    );
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $injected = TRUE;


    if (!$injected) {
      /** @var \Drupal\ws_data_sync\Plugin\WebserviceAdapterManager $webservice_adapter */
      $webservice_adapter = \Drupal::service('plugin.manager.ws_data_sync.ws_adapter');

      /** @var \Drupal\ws_data_sync\Plugin\ResponseFormatAdapterManager $format_adapter */
      $format_adapter = \Drupal::service('plugin.manager.ws_data_sync.response_format_adapter');

      /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager $authentication_adapter */
      $authentication_adapter = \Drupal::service('plugin.manager.ws_data_sync.authentication');
    }


    /** @var \Drupal\ws_data_sync\Entity\Webservice $webservice */
    $webservice = $this->entity;
    //    ksm($webservice);
    //    ksm($form_state);
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


    if (!$injected) {
      $form['type'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => $webservice_adapter->getPluginsSelectList(),
        '#default_value' => $webservice->ws_type(),
        '#description' => $this->t("Type for the Webservice."),
        '#required' => TRUE,
      ];

      $form['format'] = [
        '#type' => 'select',
        '#title' => $this->t('Remote format'),
        '#options' => $format_adapter->getPluginsSelectList(),
        '#default_value' => $webservice->getFormat(),
        '#empty_option'  => t('- select -'),
        '#description' => $this->t("Type for the Webservice."),
        //      '#required' => TRUE,
      ];

    } else {
      $form['type'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => $this->webserviceAdapter->getPluginsSelectList(),
        '#default_value' => $webservice->ws_type(),
        '#description' => $this->t("Type for the Webservice."),
        '#required' => TRUE,
      ];

      $form['format'] = [
        '#type' => 'select',
        '#title' => $this->t('Remote format'),
        '#options' => $this->formatAdapter->getPluginsSelectList(),
        '#default_value' => $webservice->getFormat(),
        '#empty_option'  => t('- select -'),
        '#description' => $this->t("Type for the Webservice."),
        //      '#required' => TRUE,
      ];
    }

    $authentication = $webservice->ws_authentication();
    $form['authentication'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

//    dpm($this->getFormId());
//    dpm($this->);
    if (!$injected) {
      $form['authentication']['type'] = [
        '#type' => 'select',
        '#title' => $this->t('Authentication'),
        '#options' => $authentication_adapter->getPluginsSelectList(),
        '#default_value' => $authentication['type'],
        '#empty_option' => t('- select -'),
        '#description' => $this->t("Authentication for the Webservice."),
        '#required' => FALSE,
        '#ajax' => [
          'trigger_as' => ['name' => 'op'],
          //        'callback' => '::changedAuthenticationTypeAjaxCallback',
          //        'options' => [
          //          'query' => [
          //            'plugin_manager' => 'fisk',
          //            //            'plugin_manager' => $authentication_adapter,
          //          ],
          //        ],
          'wrapper' => 'webservice-edit-form',
          //        'wrapper' => 'auth-params',
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
      ];
    } else {
      $form['authentication']['type'] = [
        '#type' => 'select',
        '#title' => $this->t('Authentication'),
        '#options' => $this->authenticationAdapter->getPluginsSelectList(),
        '#default_value' => $authentication['type'],
        '#empty_option' => t('- select -'),
        '#description' => $this->t("Authentication for the Webservice."),
        '#required' => FALSE,
        '#ajax' => [
          'trigger_as' => ['name' => 'op'],
          //        'callback' => '::changedAuthenticationTypeAjaxCallback',
          //        'options' => [
          //          'query' => [
          //            'plugin_manager' => 'fisk',
          //            //            'plugin_manager' => $authentication_adapter,
          //          ],
          //        ],
          'wrapper' => 'webservice-edit-form',
          //        'wrapper' => 'auth-params',
          'progress' => [
            'type' => 'throbber',
            'message' => NULL,
          ],
        ],
      ];

    }

//    dsm($authentication['type']);
    if ($authentication['type'] !== '') {
//      /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterInterface $authentication */
//      $authentication_plugin = $authentication_adapter->createInstance($authentication['type']);
//      $credentials = $authentication_plugin->getConfigParams();
//      $form['authentication']['params'] = [
//        '#title' => t('Authentication params'),
//        '#type' => 'details',
//        '#open' => TRUE,
//        '#attributes' => ['id' => 'auth-params'],
//        $credentials,
//      ];

//      dsm($form_state->getValue('authentication[type]'));
    }



//    $form['authentication']['params'] = [
//      'param-placeholder' => [
//        '#type' => 'container',
//        '#markup' => '',
//        '#attributes' => ['id' => 'auth-params'],
//      ]
//      '#title' => t('Authentication params'),
//      '#type' => 'details',
//      '#open' => TRUE,
//      '#attributes' => ['id' => 'auth-params'],
//      's_user' => [
//        '#type' => 'textfield',
//        '#title' => 'Simple User',
//        '#length' => 64,
//      ],
//      's_password' => [
//        '#type' => 'textfield',
//        '#title' => 'Simple Password',
//        '#length' => 64,
//      ],
//    ];

//    $form_state->setRebuild(true);

    //    // TODO: render param fields (using ajax) based on selected authentication
//    $form['authentication']['params']['user'] = [
//      '#type' => 'textfield',
//      '#title' => 'User',
//      '#length' => 64,
//      '#default_value' => $authentication['params']['user'],
//    ];
//
//    $form['authentication']['params']['password'] = [
//      '#type' => 'textfield',
//      '#title' => 'Password',
//      '#length' => 64,
//      '#default_value' => $authentication['params']['user'],
//    ];
    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $userInput = $form_state->getUserInput();
//    $keys = $form_state->getCleanValueKeys();
//    $newInputArray = [];
//    foreach ($keys as $key) {
//      if ($key == "op")  continue;
//      $newInputArray[$key] = $userInput[$key];
//    }
//ksm($newInputArray);
//ksm($userInput);
//ksm($keys);
//    $form_state->setUserInput($userInput);
    $form_state->setRebuild(true);
    parent::submitForm($form, $form_state); // TODO: Change the autogenerated stub
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

//    $form_state->setRedirectUrl($webservice->toUrl('collection'));
  }

  public function changedAuthenticationTypeAjaxCallback(array &$form, FormStateInterface $form_state, Request $request) {
//    dsm($request);
//    dsm($request->request->get('authentication')['type']);

    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');

    /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterManager $authentication_adapter */
    $authentication_adapter = \Drupal::service('plugin.manager.ws_data_sync.authentication');
//    $authentication_adapter = $request->query->get('plugin_manager');


    /** @var \Drupal\ws_data_sync\Plugin\AuthenticationAdapterInterface $authentication */
    $authentication = $authentication_adapter->createInstance($request->request->get('authentication')['type']);


    //    /** @var \Drupal\Core\Render\RendererInterface $renderer */
//    $renderer = \Drupal::service('renderer');

//    dsm($request->query);

    $params_container['authentication']['params'] = [
      '#title' => t('Authentication params'),
      '#type' => 'details',
      '#open' => TRUE,
      '#attributes' => ['id' => 'auth-params'],
//      's_user' => [
//        '#type' => 'textfield',
//        '#title' => 'Simple User',
//        '#length' => 64,
//      ],
//      's_password' => [
//        '#type' => 'textfield',
//        '#title' => 'Simple Password',
//        '#length' => 64,
//      ],
    ];

    $params = [
      's_user' => [
        '#type' => 'textfield',
        '#title' => 'Simple User',
        '#length' => 64,
      ],
      's_password' => [
        '#type' => 'textfield',
        '#title' => 'Simple Password',
        '#length' => 64,
      ],
    ];

//    $params_container['authentication']['params'] += $params;
//    dsm($authentication->getConfigParams());
    $params_container['authentication']['params'] += $authentication->getConfigParams();

    return $params_container['authentication'];

  }

}
