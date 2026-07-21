<?php

declare(strict_types=1);

namespace Drupal\Tests\forum\Kernel;

use Drupal\Component\Utility\DeprecationHelper;
use Drupal\Core\Logger\RfcLoggerTrait;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\user\Traits\UserCreationTrait;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Group;

use Drupal\KernelTests\KernelTestBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Defines a class for testing forum theming.
 *
 * @group forum
 */
#[Group('forum')]
#[RunTestsInSeparateProcesses]
final class ForumThemingTest extends KernelTestBase implements LoggerInterface {

  use RfcLoggerTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'history',
    'taxonomy',
    'forum',
    'comment',
    'options',
    'text',
    'field',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('comment');
    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('forum', ['forum_index', 'forum']);
    $this->installSchema('history', ['history']);
    $this->installSchema('comment', ['comment_entity_statistics']);
    $this->installConfig('node');
    $this->installConfig('forum');
    $this->container->get('theme_installer')->install(['stark']);
    $this->container->get('config.factory')->getEditable('system.theme')->set('default', 'stark')->save();
    $this->container->get('logger.factory')->addLogger($this);
    \Drupal::state()->set('comment.maintain_entity_statistics', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, string|\Stringable $message, array $context = []): void {
    if ($level <= RfcLogLevel::WARNING) {
      $this->fail(\strtr($message, $context));
    }
  }

  /**
   * Tests theming.
   */
  public function testTheming(): void {
    $forum = Term::create([
      'vid' => 'forums',
      'name' => 'General',
    ]);
    $forum->save();
    Node::create([
      'type' => 'forum',
      'status' => TRUE,
      'title' => $this->randomMachineName(),
      'taxonomy_forums' => $forum,
    ])->save();
    $manager = \Drupal::service('forum_manager');
    $user = $this->setUpCurrentUser(permissions: ['access content']);
    $index = $manager->getIndex();
    $topics = $manager->getTopics($forum->id(), $user);
    $build = [
      '#theme' => 'forums',
      '#forums' => $index->forums,
      '#topics' => $topics['topics'],
      '#parents' => [],
      '#header' => $topics['header'],
      '#term' => $index,
      '#sortby' => 1,
      '#forums_per_page' => 25,
    ];
    $renderer = \Drupal::service(RendererInterface::class);
    // @phpstan-ignore-next-line
    $out = DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3', fn () => $renderer->renderInIsolation($build), fn () => $renderer->renderPlain($build));
    $crawler = new Crawler((string) $out);
    self::assertGreaterThanOrEqual(1, $crawler->filter('a:contains("General")')->count());
  }

}
