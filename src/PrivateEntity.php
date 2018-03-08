<?php

namespace Drupal\private_entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\entity_tools\EntityTools;
use Drupal\private_entity\Plugin\Field\FieldType\PrivateEntityItem;

/**
 * Class PrivateEntity.
 */
class PrivateEntity implements PrivateEntityInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Drupal\entity_tools\EntityTools definition.
   *
   * @var \Drupal\entity_tools\EntityTools
   */
  protected $entityTools;

  /**
   * Constructs a new PrivateEntity object.
   */
  public function __construct(EntityTypeManager $entity_type_manager, EntityTools $entity_tools) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTools = $entity_tools;
  }

  /**
   * Returns an array of accounts that have access to the private entities.
   *
   * @param string $operation
   *   Operation: view, update, delete.
   *
   * @return array
   *   Array of AccountInterface instances.
   */
  public function getAccounts($operation = 'view') {
    // Possible coverage of other operations
    // currently limited to 'view'.
    return $this->entityTools->getUsersByPermission('private entity view access');
  }

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
  public function initExistingEntities($entity_type_id, $entity_bundle, $field_name) {
    $storage = $this->entityTypeManager->getStorage($entity_type_id);
    $bundleKey = $storage->getEntityType()->getKey('bundle');
    $entityQuery = \Drupal::entityQuery($entity_type_id);
    $entityQuery->condition($bundleKey, $entity_bundle);
    $entityIds = $entityQuery->execute();

    $updated = 0;
    try {
      foreach ($entityIds as $entityId) {
        $entity = $storage->loadUnchanged($entityId);
        if ($entity instanceof ContentEntityInterface) {
          // @todo review multilingual
          // @todo wait for field being created
          $entity->set($field_name, PrivateEntityItem::STATUS_PUBLIC);
          if ($entity->save() === SAVED_UPDATED) {
            ++$updated;
          }
        }
      }
    }
    catch (EntityStorageException $exception) {
      drupal_set_message($exception->getMessage(), 'error');
      // @todo logger
    }

    return $updated;
  }

}
