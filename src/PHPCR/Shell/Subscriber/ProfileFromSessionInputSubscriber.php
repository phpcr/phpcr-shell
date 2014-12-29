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
use PHPCR\Shell\Event\ProfileInitEvent;

class ProfileFromSessionInputSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::PROFILE_INIT => 'handleProfileInit',
        );
    }

    public function handleProfileInit(ProfileInitEvent $e)
    {
        $profile = $e->getProfile();
        $input = $e->getInput();

        $transportOptions = array(
            'transport' => 'name',
            'db-username' => 'db_username',
            'db-name' => 'db_name',
            'db-password' => 'db_password',
            'db-host' => 'db_host',
            'db-path' => 'db_path',
            'db-driver' => 'db_driver',
            'repo-url' => 'repo_url',
            'repo-path' => 'repo_path',
        );

        $phpcrOptions = array(
            'phpcr-username' => 'username',
            'phpcr-password' => 'password',
        );

        foreach ($transportOptions as $optionName => $configName) {
            $value = $input->getOption($optionName);

            if (!$value) {
                continue;
            }

            if (null !== $value) {
                // sanitize some input values
                switch ($optionName) {
                    case 'db-path':
                        if (!file_exists($value)) {
                            throw new \InvalidArgumentException(sprintf(
                                'DB file "%s" does not exist.', $value));
                        }

                        $value = realpath(dirname($value)) . DIRECTORY_SEPARATOR . basename($value);
                        break;
                    default:
                        // all good
                }
            }

            $profile->set('transport', $configName, (string) $value);
        }

        $workspace = $input->getArgument('workspace');

        if ($workspace) {
            $profile->set('phpcr', 'workspace', $workspace);
        }

        foreach ($phpcrOptions as $optionName => $configName) {
            if (!$input->getOption($optionName)) {
                continue;
            }

            $profile->set('phpcr', $configName, $input->getOption($optionName));
        }
    }
}
