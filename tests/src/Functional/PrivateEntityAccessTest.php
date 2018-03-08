<?php

namespace Drupal\Tests\private_entity\Functional;

use Drupal\node\Entity\NodeType;

/**
 * Tests access on Entity Test.
 *
 * @group private_entity
 */
class PrivateEntityAccessTest extends PrivateEntityTestBase {

  /**
   * A user with permission to view private entities and nodes.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $privateViewUser;

  /**
   * A user with permission to view public entities and nodes.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $publicViewUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser([
      // Node type permission.
      'access content',
      'administer nodes',
      'administer content types',
      // Entity test permission.
      'view test entity',
      'administer entity_test content',
      // Private entity permission.
      'private entity view access',
    ]);
    $this->drupalLogin($this->adminUser);

    $this->privateViewUser = $this->drupalCreateUser([
      'access content',
      'view test entity',
      'private entity view access',
    ]);

    $this->publicViewUser = $this->drupalCreateUser([
      'access content',
      'view test entity',
    ]);

    $node_type = NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ]);
    $node_type->save();

    $this->attachField('node', 'article');
    $this->attachField('entity_test', 'entity_test');
  }

  /**
   * Tests view access permissions to node.
   */
  public function testNodeViewAccess() {
    $publicNode = $this->createNode([
      'title' => 'This is public',
      "{$this->fieldName}[0][value]" => 0,
      'status' => 1,
      'type' => 'article',
    ]);
    // $publicNode->{$this->fieldName}->value = 0;
    // $publicNode->save();
    $privateNode = $this->createNode([
      'title' => 'This is private',
      "{$this->fieldName}[0][value]" => 1,
      'status' => 1,
      'type' => 'article',
    ]);
    // $privateNode->{$this->fieldName}->value = 1;
    // $privateNode->save();
    // Make sure the private_entity field is in the output.
    $this->drupalGet('node/' . $publicNode->id());
    $fields = $this->xpath('//div[contains(@class, "field--type-private-entity")]');
    // @todo returns 0
    $this->assertEquals(1, count($fields));
    $this->drupalGet('node/' . $privateNode->id());
    $fields = $this->xpath('//div[contains(@class, "field--type-private-entity")]');
    $this->assertEquals(1, count($fields));
    // Logout adminUser.
    $this->drupalLogout();

    // View as privateViewUser.
    $this->drupalLogin($this->privateViewUser);
    $this->drupalGet('node/' . $publicNode->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('node/' . $privateNode->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalLogout();

    // View as anonymous.
    $this->drupalGet('node/' . $publicNode->id());
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('node/' . $privateNode->id());
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalLogout();
  }

  /**
   * Tests view access permissions to entity_test.
   */
  public function testViewAccess() {
    // As an administrator, create a public entity
    // it should be viewable and the field should be in the output.
    $edit = [
    // Public.
      "{$this->fieldName}[0][value]" => 0,
    ];
    $this->drupalPostForm('entity_test/add', $edit, t('Save'));
    preg_match('|entity_test/manage/(\d+)|', $this->getSession()
      ->getCurrentUrl(), $match);
    $publicEntityId = $match[1];
    $this->assertSession()
      ->pageTextContains(sprintf('%s %d has been created.', 'entity_test', $publicEntityId));
    $this->assertSession()->statusCodeEquals(200);
    // Make sure the private_entity field is in the output.
    $fields = $this->xpath('//div[contains(@class, "field--type-private-entity")]');
    $this->assertEquals(1, count($fields));

    // As an administrator, create a public entity
    // it should be viewable and the field should be in the output.
    $edit = [
    // Private.
      "{$this->fieldName}[0][value]" => 1,
    ];
    $this->drupalPostForm('entity_test/add', $edit, t('Save'));
    preg_match('|entity_test/manage/(\d+)|', $this->getSession()
      ->getCurrentUrl(), $match);
    $privateEntityId = $match[1];
    $this->assertSession()
      ->pageTextContains(sprintf('%s %d has been created.', 'entity_test', $privateEntityId));
    $this->assertSession()->statusCodeEquals(200);
    // Make sure the private_entity field is in the output.
    $fields = $this->xpath('//div[contains(@class, "field--type-private-entity")]');
    $this->assertEquals(1, count($fields));

    // Tests user that has the permission to view private entities.
    $this->drupalLogout();
    $this->drupalLogin($this->privateViewUser);
    $this->drupalGet('entity_test/' . $publicEntityId);
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('entity_test/' . $privateEntityId);
    $this->assertSession()->statusCodeEquals(200);

    // Tests user that has the permission to view public entities.
    $this->drupalLogout();
    $this->drupalLogin($this->publicViewUser);
    $this->drupalGet('entity_test/' . $publicEntityId);
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('entity_test/' . $privateEntityId);
    // @todo returns 200
    $this->assertSession()->statusCodeEquals(403);
  }

}
