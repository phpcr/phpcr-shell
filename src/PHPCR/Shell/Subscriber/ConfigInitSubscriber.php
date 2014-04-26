<?php

namespace PHPCR\Shell\Subscriber;

use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Event\ApplicationInitEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber to initialize the configuration if it does not
 * already exist upon application initialization
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ConfigInitSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::APPLICATION_INIT => 'handleApplicationInit',
        );
    }

    public function handleApplicationInit(ApplicationInitEvent $event)
    {
        $application = $event->getApplication();
        $config = $application->getHelperSet()->get('config');
        $configDir = $config->getConfigDir();

        if (!file_exists($configDir)) {
            $config->initConfig();
        }
    }
}
