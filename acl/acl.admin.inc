<?php

/**
 * @file
 * Implementations of administration functions for the acl module.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Implementation of acl_edit_form().
 */
function _acl_edit_form($acl_id, $label = NULL, $new_acl = FALSE) {
  $database = \Drupal::database();

  if (!$new_acl) {
    // Ensure the ACL in question even exists.
    if (!($record = $database->query("SELECT name, figure FROM {acl} WHERE acl_id = :acl_id", [
      'acl_id' => $acl_id,
    ])->fetchAssoc())) {
      return [];
    }

    $accounts = acl_get_usernames($acl_id);
  }
  else {
    $accounts = [];
  }

  if (!isset($label)) {
    if (isset($record['name'])) {
      $label = $record['name'];
    }
    elseif (isset($record['figure'])) {
      $label = $record['figure'];
    }
    else {
      $label = $acl_id;
    }
  }

  $form = [
    '#type' => 'details',
    '#title' => Html::escape($label),
    '#tree' => TRUE,
  ];

  $form['acl_id'] = [
    '#type' => 'value',
    '#value' => $acl_id,
  ];

  $form['deletions'] = [
    '#type' => 'checkboxes',
    '#options' => [],
  ];
  $form['delete_button'] = [
    '#type' => 'button',
    '#name' => 'acl_' . $acl_id,
    '#value' => t('Remove Checked'),
    '#submit' => [],
  ];

  $form['add'] = [
    '#type' => 'entity_autocomplete',
    '#target_type' => 'user',
    '#title' => t('Add user'),
    '#maxlength' => 60,
    '#size' => 40,
    '#selection_settings' => ['include_anonymous' => FALSE],
  ];
  $form['add_button'] = [
    '#type' => 'button',
    '#name' => 'acl_' . $acl_id,
    '#value' => t('Add User'),
    '#submit' => [],
  ];

  $form['user_list'] = [
    '#type' => 'hidden',
    '#default_value' => json_encode($accounts),
  ];

  $form['user_list_changed'] = [
    '#type' => 'hidden',
    '#value' => FALSE,
  ];

  $form['#after_build'] = ['_acl_edit_form_after_build'];

  return $form;
}

/**
 * Process a form that had our buttons on it.
 */
function _acl_edit_form_after_build($form, FormStateInterface $form_state) {
  // We can't use the form values because it's the entire structure,
  // and we have no clue where our values actually are.
  // That's ok though, because #value still works for us.
  $user_list = acl_edit_form_get_user_list($form);

  $triggering_element = $form_state->getTriggeringElement();

  if (!empty($triggering_element) && $triggering_element['#value'] == $form['delete_button']['#value']) {
    $deletions = $form['deletions']['#value'];
    foreach ($deletions as $uid) {
      unset($user_list[$uid]);
      unset($form['deletions']['#value'][$uid]);
    }
  }
  elseif (!empty($triggering_element['#value']) && $triggering_element['#value'] == $form['add_button']['#value'] && !empty($form['add']['#value'])) {
    EntityAutocomplete::validateEntityAutocomplete($form['add'], $form_state, $form_state->getCompleteForm());
    $parents = array_slice($triggering_element['#parents'], 0, -1);
    $parents[] = 'add';
    $uid = $form_state->getValue($parents);
    if (!$form_state->getError($form['add']) && is_numeric($uid)) {
      $user = @User::load($uid);
      $user_list[$user->id()] = $user->getDisplayName();
      $form['add']['#value'] = '';
    }
  }

  if (count($user_list) != 0) {
    $form['deletions']['#type'] = 'checkboxes';
    $form['deletions']['#title'] = t("Current users");
    $form['deletions']['#options'] = $user_list;
    // Don't carry the value through.
    $form['deletions']['#value'] = [];
    $form['deletions'] = \Drupal::formBuilder()->doBuildForm(!empty($form['#post']) ? $form['#post']['form_id'] : 'acl_form', $form['deletions'], $form_state);
  }
  else {
    $form['delete_button']['#type'] = 'value';
  }
  $form['user_list']['#value'] = json_encode($user_list);

  $original_user_list = acl_edit_form_get_user_list($form, TRUE);
  $form['user_list_changed']['#value'] = !empty(array_diff($original_user_list, $user_list)) || !empty(array_diff($user_list, $original_user_list));
  return $form;
}

/**
 * Write the results of a form.
 *
 * The module that embedded our form must call this function!
 *
 * @param array $form
 *   The ACL edit form part of the form_state array.
 * @param null|int $priority
 *   The priority of the calling module's node access grants.
 */
function acl_save_form($form, $priority = NULL) {
  $database = \Drupal::database();

  $users = acl_edit_form_get_user_list($form);
  $database->delete('acl_user')
    ->condition('acl_id', $form['acl_id'])
    ->execute();

  $insert = $database->insert('acl_user')->fields(['acl_id', 'uid']);
  foreach ($users as $uid => $name) {
    $insert->values([
      'acl_id' => $form['acl_id'],
      'uid' => $uid,
    ]);
  }
  $insert->execute();

  if (isset($priority)) {
    $database->update('acl_node')
      ->fields([
        'priority' => $priority,
      ])
      ->condition('acl_id', $form['acl_id'])
      ->execute();
  }
}

/**
 * Decode and return the list of users.
 *
 * @param array $form
 *   The ACL form array or the ACL edit form array part of the form_state.
 * @param bool $get_default
 *   (optional) In the case of a form array, whether to return the
 *   '#default_value' (or the '#value').
 *
 * @return array
 *   An array of $uid => $username.
 */
function acl_edit_form_get_user_list(array $form, $get_default = FALSE) {
  if (is_array($form['user_list'])) {
    return json_decode($form['user_list'][$get_default ? '#default_value' : '#value'], TRUE);
  }
  return json_decode($form['user_list'], TRUE);
}

/**
 * Check whether the user_list has been changed or not.
 *
 * @param array $form
 *   The ACL form array or the ACL edit form array part of the form_state.
 *
 * @return bool
 *   TRUE if the user_list has been changed.
 */
function acl_edit_form_get_user_list_changed($form) {
  return $form['user_list_changed']['#value'] ?? $form['user_list_changed'];
}
