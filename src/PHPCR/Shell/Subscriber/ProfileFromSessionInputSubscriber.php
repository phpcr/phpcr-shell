<?php

namespace PHPCR\Shell\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Event\ProfileInitEvent;

class ProfileFromSessionInputSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::PROFILE_INIT=> 'handleProfileInit',
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
            'phpcr-workspace' => 'workspace',
        );

        foreach ($transportOptions as $optionName => $configName) {
            $value = $input->getOption($optionName);

            if (null !== $value) {
                // sanitize some input values
                switch ($optionName) {
                    case 'db-path':
                        if (!file_exists($value)) {
                            throw new \InvalidArgumentException(sprintf(
                                'DB file "%s" does not exist.'
                            , $value));
                        }

                        $value = realpath(dirname($value)) . DIRECTORY_SEPARATOR . basename($value);
                        break;
                    default:
                        // all good
                }
            }

            $profile->set('transport', $configName, (string) $value);
        }

        foreach ($phpcrOptions as $optionName => $configName) {
            $profile->set('phpcr', $configName, $input->getOption($optionName));
        }
    }
}
