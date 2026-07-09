<?php
/**
 * @file
 * Views integration.
 */

/**
 * Implements hook_views_data().
 */
function acl_views_data() {
  $data = [];

  $data['acl'] = [
    'table' => [
      'group' => t('ACL'),
      'provider' => 'acl',
      'base' => [
        'field' => 'acl_id',
        'title' => t('ACL'),
        'weight' => 10,
      ],
    ],
    'acl_id' => [
      'title' => t('ACL Id'),
      'help' => t('ACL Id: Appears in ACL'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'module' => [
      'title' => t('ACL Module name'),
      'help' => t('ACL Module: Appears in ACL'),
      'field' => ['id' => 'standard'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'string'],
      'argument' => ['id' => 'string'],
    ],
    'name' => [
      'title' => t('ACL Name'),
      'help' => t('ACL Name: Appears in ACL'),
      'field' => ['id' => 'standard'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'string'],
      'argument' => ['id' => 'string'],
    ],
    'figure' => [
      'title' => t('ACL Figure (Number)'),
      'help' => t('ACL Figure: Appears in ACL'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'acl_node' => [
      'title' => t('Relationship to ACL Node'),
      'help' => t('Relationship from ACL to ACL Node table'),
      'relationship' => [
        'base' => 'acl_node',
        'base field' => 'acl_id',
        'field' => 'acl_id',
        'id' => 'standard',
        'label' => t('ACL Node'),
      ],
    ],
    'acl_user' => [
      'title' => t('Relationship to ACL User'),
      'help' => t('Relationship from ACL to ACL User table'),
      'relationship' => [
        'base' => 'acl_user',
        'base field' => 'acl_id',
        'field' => 'acl_id',
        'id' => 'standard',
        'label' => t('ACL User'),
      ],
    ],
  ];

  $data['acl_node'] = [
    'table' => [
      'group' => t('ACL'),
      'provider' => 'acl',
    ],
    'acl_id' => [
      'title' => t('ACL Id'),
      'help' => t('ACL Id: Appears in ACL Node'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
      'relationship' => [
        'base' => 'acl',
        'base field' => 'acl_id',
        'id' => 'standard',
        'label' => t('ACL'),
        'help' => t('Relationship from ACL Node to ACL table'),
      ],
    ],
    'nid' => [
      'title' => t('Node Id'),
      'help' => t('Node Id: Appears in ACL Node'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
      'relationship' => [
        'base' => 'node_field_data',
        'base field' => 'nid',
        'id' => 'standard',
        'title' => t('Nid'),
        'label' => t('Content'),
        'help' => t('Relationship from ACL Node to Content using Node Id'),
      ],
    ],
    'grant_view' => [
      'title' => t("Grant 'view'"),
      'help' => t("Grant 'view' : Appears in ACL Node"),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'grant_update' => [
      'title' => t("Grant 'update'"),
      'help' => t("Grant 'update' : Appears in ACL Node"),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'grant_delete' => [
      'title' => t("Grant 'delete'"),
      'help' => t("Grant 'delete' : Appears in ACL Node"),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'priority' => [
      'title' => t("Priority'"),
      'help' => t("Priority : Appears in ACL Node"),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
    ],
    'acl_user' => [
      'title' => t('Relationship to ACL User'),
      'help' => t('Relationship from ACL Node to ACL User table'),
      'relationship' => [
        'base' => 'acl_user',
        'base field' => 'acl_id',
        'field' => 'acl_id',
        'id' => 'standard',
        'label' => t('ACL User'),
      ],
    ],
  ];

  $data['acl_user'] = [
    'table' => [
      'group' => t('ACL'),
      'provider' => 'acl',
    ],
    'acl_id' => [
      'title' => t('ACL Id'),
      'help' => t('ACL Id: Appears in ACL User'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
      'relationship' => [
        'base' => 'acl',
        'base field' => 'acl_id',
        'id' => 'standard',
        'title' => t('ACL'),
        'label' => t('ACL'),
        'help' => t('Relationship from ACL User to ACL table'),
      ],
    ],
    'uid' => [
      'title' => t('User Id'),
      'help' => t('User Id: Appears in ACL User'),
      'field' => ['id' => 'numeric'],
      'sort' => ['id' => 'standard'],
      'filter' => ['id' => 'numeric'],
      'argument' => ['id' => 'numeric'],
      'relationship' => [
        'base' => 'users_field_data',
        'base field' => 'uid',
        'id' => 'standard',
        'title' => t('Uid'),
        'label' => t('User'),
        'help' => t('Relationship from ACL User to User using Uid'),
      ],
    ],
    'acl_node' => [
      'title' => t('Relationship to ACL Node'),
      'help' => t('Relationship from ACL User to ACL Node table using ACL Id'),
      'relationship' => [
        'base' => 'acl_node',
        'base field' => 'acl_id',
        'field' => 'acl_id',
        'id' => 'standard',
        'label' => t('ACL Node'),
      ],
    ],
  ];

  return $data;
}

/**
 * Implements hook_views_data_alter().
 */
function acl_views_data_alter(array &$data) {
  $data['users']['acl_user'] = [
    'title' => t('Relationship to ACL User'),
    'help' => t('Relationship from User to ACL User table using Uid'),
    'relationship' => [
      'base' => 'acl_user',
      'base field' => 'uid',
      'field' => 'uid',
      'id' => 'standard',
      'label' => t('ACL User'),
    ],
  ];
}
