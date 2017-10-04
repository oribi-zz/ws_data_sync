<?php

namespace Drupal\ws_data_sync;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Url;

/**
 * Class EntityDependants.
 *
 * Contains methods for evaluating and interacting with dependent entities.
 *
 */
class EntityDependants {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Constructs a new EntityDependants object.
   */
  public function __construct(QueryFactory $entity_query, EntityTypeManagerInterface $entityTypeManager) {
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @param string $dependant_type
   * @param array $dependant_params
   *
   * @return bool
   */
  public function hasDependants($dependant_type, $dependant_params) {
    if ($this->count($dependant_type, $dependant_params) > 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * @param string $dependant_type
   * @param array $dependant_params
   *
   * @return int
   */
  public function count($dependant_type, $dependant_params) {
    /** @var \Drupal\Core\Entity\Query\QueryInterface $query */
    $query = $this->entityQuery->get($dependant_type);
    foreach ($dependant_params as $condition_key => $condition_value) {
      $query->condition($condition_key, $condition_value);
    }
    return $query->count()->execute();
  }

  public function list() {

  }

  public function disableSourceFields(&$form, $dependant_type, $dependant_params, $elements) {
    $dependants_list_url = Url::fromRoute('entity.' . $dependant_type . '.collection', $dependant_params)->getInternalPath();
    $element_description = t(
      'Field locked: Some <a href="/:field_mappings_list_link">@dependant_type</a> depend on this value', [
        ':field_mappings_list_link'=> $dependants_list_url,
        '@dependant_type' => $this->entityTypeManager->getDefinition($dependant_type)->getLowercasePluralLabel()]
    );
    foreach ($elements as $element) {
      $form[$element]['#disabled'] = TRUE;
      $form[$element]['#description'] = $element_description;
    }
  }

}
