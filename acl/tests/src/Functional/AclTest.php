<?php

namespace Drupal\Tests\acl\Functional;

use Drupal\node\Entity\NodeType;
use Drupal\node\Entity\Node;
use Drupal\Tests\BrowserTestBase;

/**
 * Test case for ACL module.
 *
 * @group Access
 */
class AclTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'acl', 'acl_node_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Implements getInfo().
   *
   * @return array
   *   Return array of info elements.
   */
  public static function getInfo() {
    return [
      'name' => 'ACL access permissions',
      'description' => 'Test ACL permissions for reading a node.',
      'group' => 'ACL',
    ];
  }

  /**
   * Implements setUp().
   */
  public function setUp(): void {
    parent::setUp();

    NodeType::create([
      'type' => 'page',
      'name' => 'Page',
    ])->save();
    NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ])->save();

    $web_user = $this->drupalCreateUser(['create page content']);
    $this->drupalLogin($web_user);
  }

  /**
   * Includes acl_create_acl, acl_delete_acl, acl_get_id_by_name.
   */
  public function testNodeAclCreateDelete() {
    // Add a node.
    $node1 = $this->drupalCreateNode(['type' => 'page', 'promote' => 0]);
    $this->assertTrue((bool) Node::load($node1->id()), 'Page node created.');

    acl_create_acl('test1', $node1->getTitle());
    $acl_id = acl_get_id_by_name('test1', $node1->getTitle());
    $this->assertNotNull($acl_id, 'ACL ID was successfully found.');
    $records = \Drupal::database()->select('acl')
      ->fields('acl', ['acl_id', 'name'])
      ->condition('acl_id', $acl_id)
      ->execute()
      ->fetchAll();
    $this->assertEquals(1, count($records), 'ACL was successfully created.');
    acl_delete_acl($records[0]->acl_id);

    $records = \Drupal::database()->select('acl')
      ->fields('acl', ['acl_id', 'name'])
      ->condition('acl_id', $records[0]->acl_id)
      ->execute()
      ->fetchAll();
    $this->assertTrue(TRUE, var_export($records, TRUE));
    $this->assertEquals(0, count($records), 'ACL was successfully removed.');
  }

  /**
   * Test node acl add/remove user.
   *
   * Includes acl_add_user, acl_remove_user, acl_has_users,
   * acl_get_id_by_name, acl_has_user, acl_get_uids.
   */
  public function testNodeAclSingleUserAddRemove() {
    // Add a node.
    $node1 = $this->drupalCreateNode(['type' => 'page', 'promote' => 0]);
    $this->assertTrue((bool) Node::load($node1->id()), 'Page node created.');

    acl_create_acl('test2', $node1->getTitle());
    // Check to see if grants added by node_test_node_access_records()
    // made it in.
    $acl_id = acl_get_id_by_name('test2', $node1->getTitle());
    $this->assertNotNull($acl_id, 'ACL ID was successfully found.');
    $records = \Drupal::database()->select('acl')
      ->fields('acl', ['acl_id', 'name'])
      ->condition('acl_id', $acl_id)
      ->execute()
      ->fetchAll();
    $this->assertEquals(1, count($records), 'ACL was successfully created.');

    // Add user (can't we use the user created in setup?).
    $web_user_1 = $this->drupalCreateUser();
    // $this->drupalLogin($web_user);
    acl_add_user($acl_id, $web_user_1->id());
    $records = \Drupal::database()->select('acl_user')
      ->fields('acl_user', ['acl_id', 'uid'])
      ->condition('uid', $web_user_1->id())
      ->execute()
      ->fetchAll();
    // Verify user is added.
    $this->assertEquals(1, count($records), 'User was successfully added.');

    // Remove user.
    acl_remove_user($acl_id, $web_user_1->id());
    $records = \Drupal::database()->select('acl_user')
      ->fields('acl_user', ['acl_id', 'uid'])
      ->condition('uid', $web_user_1->id())
      ->execute()
      ->fetchAll();
    // Verify user is removed.
    $this->assertEquals(0, count($records), 'User was successfully removed.');
  }

  /**
   * Includes acl_node_add_acl, acl_node_remove_acl, acl_node_clear_acls.
   */
  public function testNodeAclAddRemoveFromNode() {
    // Add nodes.
    $node1 = $this->drupalCreateNode(['type' => 'page', 'promote' => 0]);
    $this->assertTrue((bool) Node::load($node1->id()), 'Page node created.');
    $node2 = $this->drupalCreateNode(['type' => 'page', 'promote' => 0]);
    $this->assertTrue((bool) Node::load($node2->id()), 'Page node created.');
    $node3 = $this->drupalCreateNode(['type' => 'page', 'promote' => 0]);
    $this->assertTrue((bool) Node::load($node3->id()), 'Page node created.');

    // Create an ACL and add nodes.
    $acl_id1 = acl_create_acl('test3', 'test', 1);
    $this->assertNotNull($acl_id1, 'ACL ID was created.');
    // Add two nodes.
    $query = \Drupal::database()->select('node', 'n')
      ->fields('n', ['nid'])
      ->condition('nid', [$node1->id(), $node2->id()], 'IN');
    acl_add_nodes($query, $acl_id1, 1, 1, 1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(2, $count, "2 nodes under control ($count).");
    // Add a third node.
    acl_node_add_acl($node3->id(), $acl_id1, 1, 1, 1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(3, $count, '3 nodes under control.');
    // Add the second node again.
    acl_node_add_acl($node2->id(), $acl_id1, 1, 1, 1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(3, $count, 'Still only 3 nodes under control.');

    // Remove the second node again.
    acl_node_remove_acl($node2->id(), $acl_id1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(2, $count, '2 nodes left under control.');
    // Remove the second node again.
    acl_node_remove_acl($node2->id(), $acl_id1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(2, $count, 'Still 2 nodes left under control.');

    // Create another ACL and add nodes.
    $acl_id2 = acl_create_acl('test3', 'test', 2);
    $this->assertNotNull($acl_id2, 'ACL ID was created.');
    acl_node_add_acl($node1->id(), $acl_id2, 1, 1, 1);
    acl_node_add_acl($node2->id(), $acl_id2, 1, 1, 1);
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id2)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(2, $count, '2 nodes under control.');
    // Remove a node (which has two ACLs).
    acl_node_clear_acls($node1->id(), 'test3');
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id1)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(1, $count, '1 node left under control.');
    $count = \Drupal::database()->select('acl_node')
      ->condition('acl_id', $acl_id2)
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertEquals(1, $count, '1 node left under control.');
  }

  // @todo Test to be implemented.
  // /**
  // * Included acl_node_delete.
  // */
  // public function testNodeAclAddAndRemoveNode() {
  // }
  //
  // /**
  // * Included acl_user_cancel.
  // */
  // public function testNodeAclAddAndRemoveUser() {
  // }
  //
  // /**
  // * Test node ACL permissions check.
  // *
  // * Includes independent check of the permissions by checking the grants
  // * table AND by trying to view the node as a unauthorized user and an
  // * authorized user for each of the 3 use cases (view, edit, delete).
  // */
  // public function testNodeAclPermissionCheck() {
  // }
}
