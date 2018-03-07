<?php

namespace Drupal\Tests\private_entity\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Sets up private_entity field test and helpers definition.
 *
 * @group private_entity
 */
abstract class PrivateEntityTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field',
    'field_ui',
    'entity_test',
    'private_entity',
  ];

  /**
   * A user with permission to create test entities.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * An array of display options to pass to EntityViewDisplay.
   *
   * @var array
   */
  protected $displayOptions;

  /**
   * A field storage to use in this test class.
   *
   * @var \Drupal\field\Entity\FieldStorageConfig
   */
  protected $fieldStorage;

  /**
   * The private_entity field used in this test class.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $field;

  /**
   * Attaches a field to an entity type.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $entity_bundle
   *   The entity bundle.
   */
  protected function attachField($entity_type_id, $entity_bundle) {
    $field_name = 'field_private';
    $type = 'private_entity';
    $widget_type = 'private_entity_default_widget';
    $formatter_type = 'private_entity_default_formatter';

    // Add the private_entity field to the entity type.
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type_id,
      'type' => $type,
      // 'cardinality' => -1,
      // 'translatable' => FALSE,.
    ]);
    $this->fieldStorage->save();
    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'label' => 'Private',
      // 'entity_type' => $entity_type_id, // should not be necessary.
      'bundle' => $entity_bundle,
      'required' => TRUE,
    ]);
    $this->field->save();

    EntityFormDisplay::load($entity_type_id . '.' . $entity_bundle . '.default')
      ->setComponent($field_name, ['type' => $widget_type])
      ->save();

    $this->displayOptions = [
      'type' => $formatter_type,
      'label' => 'Private',
    ];

    EntityViewDisplay::create([
      'targetEntityType' => $this->field->getTargetEntityTypeId(),
      'bundle' => $this->field->getTargetBundle(),
      'mode' => 'full',
      'status' => TRUE,
    ])->setComponent($field_name, $this->displayOptions)->save();
  }

  /**
   * Returns the entity form for an entity type and bundle.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $entity_bundle
   *   The entity bundle.
   * @param string $operation
   *   Operation: add, edit or delete.
   *
   * @return string
   *   The path to the entity form.
   */
  protected function getEntityTypeFormPath($entity_type_id, $entity_bundle, $operation = 'add') {
    $result = '';
    switch ($entity_type_id) {
      case 'entity_test':
        $result = $entity_type_id . '/' . $operation;
        break;
    }
    return $result;
  }

}
