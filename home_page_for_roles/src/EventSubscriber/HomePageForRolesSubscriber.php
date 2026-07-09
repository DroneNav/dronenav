<?php

namespace Drupal\home_page_for_roles\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\path_alias\AliasManagerInterface as PathAliasManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Event Subscriber for homepage redirections.
 */
class HomePageForRolesSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface|\Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Constructs a new HomePageForRolesSubscriber object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Path\AliasManagerInterface|\Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   */
  public function __construct(
    AccountProxyInterface $current_user,
    ConfigFactoryInterface $config_factory,
    PathValidatorInterface $path_validator,
    $alias_manager
  ) {
    $this->currentUser = $current_user;
    $this->configFactory = $config_factory;
    $this->pathValidator = $path_validator;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onKernelRequest', 28],
    ];
  }

  /**
   * Handles the kernel request event.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The request event.
   */
  public function onKernelRequest(RequestEvent $event) {
    $request = $event->getRequest();
    
    // Check for post-login redirection first (for authenticated users)
    if ($this->currentUser->isAuthenticated()) {
      $tempstore = \Drupal::service('tempstore.private')->get('home_page_for_roles');
      $redirect_path = $tempstore->get('redirect_path');
      
      if ($redirect_path) {
        // Clear the stored redirect
        $tempstore->delete('redirect_path');
        
        // Perform the redirect
        if ($this->pathValidator->isValid($redirect_path)) {
          $event->setResponse(new RedirectResponse($redirect_path));
          return;
        }
      }
    }
    
    // Only act on the homepage for regular redirections
    if ($request->getPathInfo() !== '/') {
      return;
    }

    $config = $this->configFactory->get('home_page_for_roles.settings');
    $redirect_path = NULL;

    if (!$this->currentUser->isAuthenticated()) {
      // Anonymous users
      $redirect_path = $config->get('anonymous_homepage');
    }
    else {
      // Authenticated users - check role-specific homepages
      $role_homepages = $config->get('role_homepages') ?? [];
      
      // Priority for administrator
      $user_roles = $this->currentUser->getRoles();
      if (in_array('administrator', $user_roles) && !empty($role_homepages['administrator'])) {
        $redirect_path = $role_homepages['administrator'];
      }
      else {
        // Check other roles (exclude 'authenticated' as it's the default)
        foreach ($user_roles as $role) {
          if ($role !== 'authenticated' && !empty($role_homepages[$role])) {
            $redirect_path = $role_homepages[$role];
            break;
          }
        }
      }
      
      // If no role-specific homepage, use default authenticated homepage
      if (!$redirect_path) {
        $redirect_path = $config->get('authenticated_homepage');
      }
    }

    // Perform redirect if a path is set and is valid
    if ($redirect_path && $redirect_path !== '/') {
      // If it's an internal path, get its alias
      if (strpos($redirect_path, '/node/') === 0) {
        $redirect_path = $this->aliasManager->getAliasByPath($redirect_path);
      }
      
      if ($this->pathValidator->isValid($redirect_path)) {
        $event->setResponse(new RedirectResponse($redirect_path));
      }
    }
  }

}