<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPCR\Shell\PhpcrShell;

class Container extends ContainerBuilder
{
    protected $mode;

    /**
     * @var array Transports
     */
    protected $transports = array(
        'transport.transport.doctrinedbal' => 'PHPCR\Shell\Transport\Transport\DoctrineDbal',
        'transport.transport.jackrabbit' => 'PHPCR\Shell\Transport\Transport\Jackrabbit',
        'transport.transport.fs' => 'PHPCR\Shell\Transport\Transport\JackalopeFs',
    );

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
    }

    public function registerHelpers()
    {
        $this->register('helper.question', 'Symfony\Component\Console\Helper\DialogHelper');
        $this->register('helper.editor', 'PHPCR\Shell\Console\Helper\EditorHelper');
        $this->register('helper.path', 'PHPCR\Shell\Console\Helper\PathHelper');
        $this->register('helper.repository', 'PHPCR\Shell\Console\Helper\RepositoryHelper')
            ->addArgument(new Reference('phpcr.session_manager'));
        $this->register('helper.text', 'PHPCR\Shell\Console\Helper\TextHelper');
        $this->register('helper.node', 'PHPCR\Shell\Console\Helper\NodeHelper');
        $this->register('helper.result_formatter', 'PHPCR\Shell\Console\Helper\ResultFormatterHelper')
            ->addArgument(new Reference('helper.text'))
            ->addArgument(new Reference('helper.table'))
            ->addArgument(new Reference('config.config.phpcrsh'));
        $this->register('helper.table', 'PHPCR\Shell\Console\Helper\TableHelper');
    }

    public function registerConfig()
    {
        $this->register('config.manager', 'PHPCR\Shell\Config\ConfigManager')
            ->addArgument(new Reference('helper.question'));

        $this->register('config.profile', 'PHPCR\Shell\Config\Profile');
        $this->register('config.profile_loader', 'PHPCR\Shell\Config\ProfileLoader')
            ->addArgument(new Reference('config.manager'));
        $this->register('config.config.phpcrsh', 'PHPCR\Shell\Config\Config')
            ->setFactoryService('config.manager')
            ->setFactoryMethod('getPhpcrshConfig');
    }

    public function registerPhpcr()
    {
        // transports
        foreach ($this->transports as $id => $class) {
            $this->register($id, $class)->addArgument(new Reference('config.profile'));
        }

        $registry = $this->register('phpcr.transport_registry', 'PHPCR\Shell\Transport\TransportRegistry');

        foreach (array_keys($this->transports) as $transportId) {
            $registry->addMethodCall('register', array(new Reference($transportId)));
        }

        $this->register('phpcr.session_manager.active', 'PHPCR\Shell\Phpcr\SessionManager')
            ->addArgument(new Reference('phpcr.transport_registry'))
            ->addArgument(new Reference('config.profile'));

        $this->register('phpcr.session_manager.passive', 'PHPCR\Shell\Phpcr\SessionManager')
            ->addArgument(new Reference('phpcr.transport_registry'))
            ->addArgument(new Reference('config.profile'));

        $this->setAlias('phpcr.session_manager', 'phpcr.session_manager.active');

        $repositoryDefinition = $this->register('phpcr.repository');
        $sessionDefinition = $this->register('phpcr.session');

        $repositoryDefinition->setFactoryService('phpcr.session_manager')->setFactoryMethod('getRepository');
        $sessionDefinition->setFactoryService('phpcr.session_manager')->setFactoryMethod('getSession');
    }

    public function registerEvent()
    {
        if ($this->mode === PhpcrShell::MODE_STANDALONE) {
            $this->register(
                'event.subscriber.profile_loader',
                'PHPCR\Shell\Subscriber\ProfileLoaderSubscriber'
            )
                ->addArgument(new Reference('config.profile_loader'))
                ->addArgument(new Reference('helper.question'))
                ->addTag('event.subscriber');

            $this->register(
                'event.subscriber.profile_from_session_input',
                'PHPCR\Shell\Subscriber\ProfileFromSessionInputSubscriber'
            )->addTag('event.subscriber');

            $this->register(
                'event.subscriber.profile_writer',
                'PHPCR\Shell\Subscriber\ProfileWriterSubscriber'
            )
                ->addArgument(new Reference('config.profile_loader'))
                ->addArgument(new Reference('helper.question'))
                ->addTag('event.subscriber');

            $this->register(
                'event.subscriber.config_init',
                'PHPCR\Shell\Subscriber\ConfigInitSubscriber'
            )
                ->addArgument(new Reference('config.manager'))
                ->addTag('event.subscriber');
        }

        $this->register(
            'event.subscriber.alias',
            'PHPCR\Shell\Subscriber\AliasSubscriber'
        )
            ->addArgument(new Reference('config.manager'))
            ->addTag('event.subscriber');

        $this->register(
            'event.subscriber.exception',
            'PHPCR\Shell\Subscriber\ExceptionSubscriber'
        )->addTag('event.subscriber');

        $dispatcher = $this->register('event.dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');

        foreach (array_keys($this->findTaggedServiceIds('event.subscriber')) as $id) {
            $dispatcher->addMethodCall('addSubscriber', array(new Reference($id)));
        }
    }

    public function registerConsole()
    {
        if ($this->mode === PhpcrShell::MODE_STANDALONE) {
            $this->register('application', 'PHPCR\Shell\Console\Application\ShellApplication')
                ->addArgument(new Reference('container'));
        } else {
            $this->register('application', 'PHPCR\Shell\Console\Application\EmbeddedApplication')
                ->addArgument(new Reference('container'));
        }

        $this->register('console.input.autocomplete', 'PHPCR\Shell\Console\Input\AutoComplete')
            ->addArgument(new Reference('application'))
            ->addArgument(new Reference('phpcr.session'));
    }

    public function getMode()
    {
        return $this->mode;
    }
}
