<?php

namespace Drupal\Tests\private_entity\Functional;

/**
 * Tests access on Entity Test.
 *
 * @group private_entity
 */
class PrivateEntityAccessTest extends PrivateEntityTestBase {

  /**
   * The entity type id used for this test.
   */
  const ENTITY_TYPE_ID = 'entity_test';

  /**
   * The entity bundle used for this test.
   */
  const ENTITY_BUNDLE = 'entity_test';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser([
      // Entity test permission.
      'view test entity',
      // Private entity permission.
      'private entity view access',
    ]);
    $this->drupalLogin($this->webUser);

    $this->attachField(self::ENTITY_TYPE_ID, self::ENTITY_BUNDLE);
  }

  /**
   * Tests access permissions to entity view.
   */
  public function testViewAccess() {
    // @todo
  }

}
