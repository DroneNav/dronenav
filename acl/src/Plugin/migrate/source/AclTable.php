<?php

namespace Drupal\acl\Plugin\migrate\source;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6/7 ACL Table source from database.
 *
 * @MigrateSource(
 *   id = "d6_d7_acl_table",
 *   source_module = "acl",
 * )
 */
class AclTable extends DrupalSqlBase {

  /**
   * Table name to fetch.
   *
   * @var string
   */
  protected $tableName;

  /**
   * Field names to fetch.
   *
   * @var array
   */
  protected $fieldsList;

  /**
   * Default Ids for Migrate API.
   *
   * @var array
   */
  protected $ids = [
    'acl_id' => [
      'type' => 'integer',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_type_manager);
    $this->tableName = $configuration['table_name'];
    $this->fieldsList = $configuration['fields_list'];

    if (!empty($configuration['ids'])) {
      $this->ids = $configuration['ids'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select($this->tableName, 'a')
      ->fields('a', $this->fieldsList);
    $query->orderBy('acl_id');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array_combine($this->fieldsList, $this->fieldsList);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return $this->ids;
  }

}
