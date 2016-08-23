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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Event\CommandPreRunEvent;
use PHPCR\Shell\Console\Input\StringInput;
use PHPCR\Shell\Config\ConfigManager;

/**
 * Check to see if the input references a command alias and
 * modify the input to represent the command which it represents.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class AliasSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::COMMAND_PRE_RUN => 'handleAlias',
        );
    }

    /**
     * Check for an alias and replace the input with a new string command
     * if the alias exists.
     *
     * @return string New command string (for testing purposes)
     */
    public function handleAlias(CommandPreRunEvent $event)
    {
        $input = $event->getInput();

        $commandName = $input->getFirstArgument();

        $aliasConfig = $this->configManager->getConfig('alias');

        if (!isset($aliasConfig[$commandName])) {
            return;
        }

        $command = $aliasConfig[$commandName];
        $command = $command .= substr($input->getRawCommand(), strlen($commandName));

        $newInput = new StringInput($command);
        $event->setInput($newInput);

        return $command;
    }
}
