<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\DependencyInjection;

use DTL\Glob\GlobHelper;
use PHPCR\Shell\Config\Config;
use PHPCR\Shell\Config\ConfigManager;
use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\Config\ProfileLoader;
use PHPCR\Shell\Console\Application\EmbeddedApplication;
use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Console\Helper\EditorHelper;
use PHPCR\Shell\Console\Helper\NodeHelper;
use PHPCR\Shell\Console\Helper\PathHelper;
use PHPCR\Shell\Console\Helper\RepositoryHelper;
use PHPCR\Shell\Console\Helper\ResultFormatterHelper;
use PHPCR\Shell\Console\Helper\TextHelper;
use PHPCR\Shell\Console\Input\AutoComplete;
use PHPCR\Shell\Phpcr\SessionManager;
use PHPCR\Shell\PhpcrShell;
use PHPCR\Shell\Query\UpdateProcessor;
use PHPCR\Shell\Subscriber\AliasSubscriber;
use PHPCR\Shell\Subscriber\ConfigInitSubscriber;
use PHPCR\Shell\Subscriber\ExceptionSubscriber;
use PHPCR\Shell\Subscriber\ProfileFromSessionInputSubscriber;
use PHPCR\Shell\Subscriber\ProfileLoaderSubscriber;
use PHPCR\Shell\Subscriber\ProfileWriterSubscriber;
use PHPCR\Shell\Transport\Transport\DoctrineDbal;
use PHPCR\Shell\Transport\Transport\JackalopeFs;
use PHPCR\Shell\Transport\Transport\Jackrabbit;
use PHPCR\Shell\Transport\TransportRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Container extends ContainerBuilder
{
    protected $mode;

    /**
     * @var array Transports
     */
    protected $transports = [
        'transport.transport.doctrinedbal' => DoctrineDbal::class,
        'transport.transport.jackrabbit'   => Jackrabbit::class,
        'transport.transport.fs'           => JackalopeFs::class,
    ];

    public function __construct($mode = PhpcrShell::MODE_STANDALONE)
    {
        parent::__construct();
        $this->mode = $mode;

        $this->set('container', $this);

        $this->registerHelpers();
        $this->registerConfig();
        $this->registerPhpcr();
        $this->registerEvent();
        $this->registerConsole();
        $this->registerQuery();
    }

    public function registerHelpers()
    {
        $this->register('helper.question', QuestionHelper::class);
        $this->register('helper.editor', EditorHelper::class);
        $this->register('helper.path', PathHelper::class);
        $this->register('helper.repository', RepositoryHelper::class)
            ->addArgument(new Reference('phpcr.session_manager'));
        $this->register('helper.text', TextHelper::class);
        $this->register('helper.node', NodeHelper::class);
        $this->register('helper.result_formatter', ResultFormatterHelper::class)
            ->addArgument(new Reference('helper.text'))
            ->addArgument(new Reference('config.config.phpcrsh'));
    }

    public function registerConfig()
    {
        $this->register('config.manager', ConfigManager::class)
            ->addArgument(new Reference('helper.question'));

        $this->register('config.profile', Profile::class);
        $this->register('config.profile_loader', ProfileLoader::class)
            ->addArgument(new Reference('config.manager'));
        $this->register('config.config.phpcrsh', Config::class)
            ->setFactory([new Reference('config.manager'), 'getPhpcrshConfig']);
    }

    public function registerPhpcr()
    {
        // transports
        foreach ($this->transports as $id => $class) {
            $this->register($id, $class)->addArgument(new Reference('config.profile'));
        }

        $registry = $this->register('phpcr.transport_registry', TransportRegistry::class);

        foreach (array_keys($this->transports) as $transportId) {
            $registry->addMethodCall('register', [new Reference($transportId)]);
        }

        $this->register('phpcr.session_manager.active', SessionManager::class)
            ->addArgument(new Reference('phpcr.transport_registry'))
            ->addArgument(new Reference('config.profile'));

        $this->register('phpcr.session_manager.passive', SessionManager::class)
            ->addArgument(new Reference('phpcr.transport_registry'))
            ->addArgument(new Reference('config.profile'));

        $this->setAlias('phpcr.session_manager', 'phpcr.session_manager.active');

        $repositoryDefinition = $this->register('phpcr.repository');
        $sessionDefinition = $this->register('phpcr.session');

        $repositoryDefinition->setFactory([new Reference('phpcr.session_manager'), 'getRepository']);
        $sessionDefinition->setFactory([new Reference('phpcr.session_manager'), 'getSession']);

        $this->register('dtl.glob.helper', GlobHelper::class);
    }

    public function registerEvent()
    {
        if ($this->mode === PhpcrShell::MODE_STANDALONE) {
            $this->register(
                'event.subscriber.profile_from_session_input',
                ProfileFromSessionInputSubscriber::class
            )->addTag('event.subscriber');

            $this->register(
                'event.subscriber.profile_loader',
                ProfileLoaderSubscriber::class
            )
                ->addArgument(new Reference('config.profile_loader'))
                ->addArgument(new Reference('helper.question'))
                ->addTag('event.subscriber');

            $this->register(
                'event.subscriber.profile_writer',
                ProfileWriterSubscriber::class
            )
                ->addArgument(new Reference('config.profile_loader'))
                ->addArgument(new Reference('helper.question'))
                ->addTag('event.subscriber');

            $this->register(
                'event.subscriber.config_init',
                ConfigInitSubscriber::class
            )
                ->addArgument(new Reference('config.manager'))
                ->addTag('event.subscriber');
        }

        $this->register(
            'event.subscriber.alias',
            AliasSubscriber::class
        )
            ->addArgument(new Reference('config.manager'))
            ->addTag('event.subscriber');

        $this->register(
            'event.subscriber.exception',
            ExceptionSubscriber::class
        )->addTag('event.subscriber');

        $dispatcher = $this->register('event.dispatcher', EventDispatcher::class);

        foreach (array_keys($this->findTaggedServiceIds('event.subscriber')) as $id) {
            $dispatcher->addMethodCall('addSubscriber', [new Reference($id)]);
        }
    }

    public function registerConsole()
    {
        if ($this->mode === PhpcrShell::MODE_STANDALONE) {
            $this->register('application', ShellApplication::class)
                ->addArgument(new Reference('container'));
        } else {
            $this->register('application', EmbeddedApplication::class)
                ->addArgument(new Reference('container'));
        }

        $this->register('console.input.autocomplete', AutoComplete::class)
            ->addArgument(new Reference('application'))
            ->addArgument(new Reference('phpcr.session'));
    }

    public function registerQuery()
    {
        $this->register('query.update.expression_language', ExpressionLanguage::class);
        $this->register('query.update.processor', UpdateProcessor::class)
            ->addArgument(new Reference('query.update.expression_language'));
    }

    public function getMode()
    {
        return $this->mode;
    }
}
