<?php

namespace PHPCR\Shell\Subscriber;

use Jackalope\NotImplementedException;
use PHPCR\Shell\Event\CommandExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPCR\Shell\Event\PhpcrShellEvents;

/**
 * Try and better handle exceptions
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::COMMAND_EXCEPTION => 'handleException',
        );
    }

    public function handleException(CommandExceptionEvent $event)
    {
        $exception = $event->getException();
        $output = $event->getOutput();

        if ($exception instanceof NotImplementedException) {
            $output->writeln('<error>Not implemented: ' . $exception->getMessage() . '</error>');
        }

        $output->writeln('<error>[' . get_class($exception) .'] ' . $exception->getMessage() . '</error>');
    }
}
