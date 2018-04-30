<?php

namespace Drupal\private_entity;

/**
 * Interface PrivateEntityInterface.
 */
interface PrivateEntityInterface {

  /**
   * Returns an array of accounts that have access to the private entities.
   *
   * @param array $operations
   *   List of operations covered by the grant: view, update, delete.
   *
   * @return array
   *   Array of AccountInterface instances.
   */
  public function getGrantedAccounts(array $operations);

  /**
   * Initializes the value of existing entities to public.
   *
   * @param string $entity_type_id
   *   Entity type id.
   * @param string $entity_bundle
   *   Entity bundle name.
   * @param string $field_name
   *   Field name.
   *
   * @return int
   *   Amount of entries that were updated.
   */
  public function initExistingEntities($entity_type_id, $entity_bundle, $field_name);

}
