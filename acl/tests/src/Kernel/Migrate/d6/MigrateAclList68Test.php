<?php

namespace Drupal\Tests\acl\Kernel\Migrate\d6;

use Drupal\Tests\acl\Kernel\Migrate\AclMigrationTestTrait;
use Drupal\Tests\migrate_drupal\Kernel\d6\MigrateDrupal6TestBase;

/**
 * Tests migration of ACL columns from Drupal 6 to Drupal 8.
 *
 * @group acl
 */
class MigrateAclList68Test extends MigrateDrupal6TestBase {

  use AclMigrationTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'acl',
    'acl_node_test',
    'menu_ui',
    'migrate_drupal',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->loadFixture(__DIR__ . '/../../../../fixtures/d6_d7_table.php');
    $this->installSchema('acl', ['acl', 'acl_user', 'acl_node']);
    $this->installSchema('node', ['node_access']);

    $this->executeMigration('d6_d7_acl');
    $this->executeMigration('d6_d7_acl_user');
    $this->executeMigration('d6_d7_acl_node');

    $this->migrateContent();
  }

}
