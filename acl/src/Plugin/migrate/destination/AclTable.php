<?php

namespace Drupal\acl\Plugin\migrate\destination;

use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Drupal 8 ACL Table destination.
 *
 * @MigrateDestination(
 *   id = "acl_table",
 *   destination_module="acl",
 * )
 */
class AclTable extends DestinationBase implements ContainerFactoryPluginInterface {

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
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    protected Connection $connection,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->tableName = $this->configuration['table_name'];
    $this->fieldsList = $this->configuration['fields_list'];

    if (!empty($this->configuration['ids'])) {
      $this->ids = $this->configuration['ids'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL) {
    return new static($configuration, $plugin_id, $plugin_definition, $migration, $container->get('database'));
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $destination = $row->getDestination();

    $keys = [];
    foreach (array_keys($this->ids) as $id) {
      $keys[$id] = $destination[$id];
      unset($destination[$id]);
    }

    $this->connection
      ->merge($this->tableName)
      ->keys($keys)
      ->fields($destination)
      ->execute();

    $return = array_map(function ($key) use ($row) {
      return $row->getDestinationProperty($key);
    }, $keys);

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return $this->ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(?MigrationInterface $migration = NULL) {
    return array_combine($this->fieldsList, $this->fieldsList);
  }

}
