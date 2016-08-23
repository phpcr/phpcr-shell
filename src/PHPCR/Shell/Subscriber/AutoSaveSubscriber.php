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

namespace PHPCR\Shell\Subscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Automatically save on console terminate event.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class AutoSaveSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::TERMINATE => 'handleTerminate',
        ];
    }

    public function handleTerminate(ConsoleTerminateEvent $event)
    {
        $command = $event->getCommand();
        $output = $event->getOutput();
        $session = $command->get('phpcr.session');

        if ($session->hasPendingChanges()) {
            $output->writeln('<info>Auto-saving session</info>');
        }
        $session->save();
    }
}
