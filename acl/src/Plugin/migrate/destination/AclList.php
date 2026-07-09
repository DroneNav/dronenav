<?php

namespace Drupal\acl\Plugin\migrate\destination;

use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Drupal 8 ACL List Table destination.
 *
 * @MigrateDestination(
 *   id = "acl_list",
 *   destination_module="acl",
 * )
 */
class AclList extends DestinationBase implements ContainerFactoryPluginInterface {

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
    $this->connection
      ->merge('acl')
      ->key('acl_id', $destination['acl_id'])
      ->fields([
        'module' => $destination['module'],
        'name' => $destination['name'],
        'figure' => $destination['figure'],
      ])
      ->execute();
    return [$row->getDestinationProperty('acl_id')];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['acl_id']['type'] = 'integer';

    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(?MigrationInterface $migration = NULL) {
    return [
      'acl_id' => $this->t('Primary key: unique ACL ID.'),
      'module' => $this->t('The name of the module that created this ACL entry.'),
      'name'   => $this->t('A name (or other identifying information) for this ACL entry, given by the module that created it.'),
      'figure' => $this->t('A number for this ACL entry, given by the module that created it.'),
    ];
  }

}
