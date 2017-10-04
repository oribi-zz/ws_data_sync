<?php

namespace Drupal\ws_data_sync;
use Drupal\Core\Entity\Query\QueryFactory;

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
   * Constructs a new EntityDependants object.
   */
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
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

}
