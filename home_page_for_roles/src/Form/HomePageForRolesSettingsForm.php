<?php

namespace Drupal\home_page_for_roles\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\user\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HomePageForRolesSettingsForm extends ConfigFormBase {

  protected $pathValidator;

  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typed_config, PathValidatorInterface $path_validator) {
    parent::__construct($config_factory, $typed_config);
    $this->pathValidator = $path_validator;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('path.validator')
    );
  }

  protected function getEditableConfigNames() {
    return ['home_page_for_roles.settings'];
  }

  public function getFormId() {
    return 'home_page_for_roles_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('home_page_for_roles.settings');

    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Configure different homepage redirections based on user roles. Paths can be internal (e.g., /node/1, /about) or external URLs.') . '</p>',
    ];

    // Default homepage for anonymous users
    $form['anonymous_homepage'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Anonymous Users Homepage'),
      '#description' => $this->t('Enter the path (e.g., /node/1, /welcome, or external URL)'),
      '#default_value' => $config->get('anonymous_homepage') ?? '/',
      '#required' => TRUE,
    ];

    // Default homepage for authenticated users
    $form['authenticated_homepage'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Authenticated Users Homepage'),
      '#description' => $this->t('Default homepage for logged-in users without specific role settings'),
      '#default_value' => $config->get('authenticated_homepage') ?? '/user',
      '#required' => TRUE,
    ];

    // Get all roles except anonymous and authenticated
    $roles = Role::loadMultiple();
    unset($roles['anonymous'], $roles['authenticated']);
    
    if (!empty($roles)) {
      $form['role_homepages'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Role-specific Homepages'),
        '#description' => $this->t('Leave empty to use the default authenticated homepage. Higher priority roles (first match) will be used if a user has multiple roles.'),
        '#tree' => TRUE,
      ];

      foreach ($roles as $role_id => $role) {
        $form['role_homepages'][$role_id] = [
          '#type' => 'textfield',
          '#title' => $this->t('@role Homepage', ['@role' => $role->label()]),
          '#description' => $this->t('Homepage for users with the @role role', ['@role' => $role->label()]),
          '#default_value' => $config->get("role_homepages.$role_id"),
        ];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate anonymous homepage
    $anonymous_path = $form_state->getValue('anonymous_homepage');
    if ($anonymous_path && $anonymous_path !== '/' && !$this->isValidPath($anonymous_path)) {
      $form_state->setErrorByName('anonymous_homepage', $this->t('Invalid path for anonymous homepage.'));
    }

    // Validate authenticated homepage
    $auth_path = $form_state->getValue('authenticated_homepage');
    if ($auth_path && $auth_path !== '/' && !$this->isValidPath($auth_path)) {
      $form_state->setErrorByName('authenticated_homepage', $this->t('Invalid path for authenticated homepage.'));
    }

    // Validate role-specific homepages
    $role_homepages = $form_state->getValue('role_homepages');
    if ($role_homepages) {
      foreach ($role_homepages as $role_id => $path) {
        if ($path && $path !== '/' && !$this->isValidPath($path)) {
          $form_state->setErrorByName("role_homepages][$role_id", $this->t('Invalid path for @role homepage.', ['@role' => $role_id]));
        }
      }
    }
  }

  private function isValidPath($path) {
    // Check if it's an external URL
    if (filter_var($path, FILTER_VALIDATE_URL)) {
      return TRUE;
    }

    // Check if it's a valid internal path
    return $this->pathValidator->isValid($path);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('home_page_for_roles.settings');
    
    // Clean empty role homepage values
    $role_homepages = array_filter($form_state->getValue('role_homepages') ?: []);
    
    $config->set('anonymous_homepage', $form_state->getValue('anonymous_homepage'))
           ->set('authenticated_homepage', $form_state->getValue('authenticated_homepage'))
           ->set('role_homepages', $role_homepages)
           ->save();

    $this->messenger()->addMessage($this->t('Homepage by role settings have been saved.'));
    parent::submitForm($form, $form_state);
  }
}