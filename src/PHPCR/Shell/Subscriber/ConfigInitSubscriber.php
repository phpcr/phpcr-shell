<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Subscriber;

use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Event\ApplicationInitEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPCR\Shell\Config\ConfigManager;

/**
 * Subscriber to initialize the configuration if it does not
 * already exist upon application initialization
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ConfigInitSubscriber implements EventSubscriberInterface
{
    private $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::APPLICATION_INIT => 'handleApplicationInit',
        );
    }

    public function handleApplicationInit(ApplicationInitEvent $event)
    {
        $configDir = $this->configManager->getConfigDir();

        if (!file_exists($configDir)) {
            $this->configManager->initConfig();
        }
    }
}
