<?php

namespace PHPCR\Shell\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPCR\Shell\Event\PhpcrShellEvents;
use PHPCR\Shell\Event\ProfileInitEvent;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPCR\Shell\Config\ProfileLoader;

class ProfileWriterSubscriber implements EventSubscriberInterface
{
    protected $profileLoader;
    protected $dialogHelper;

    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::PROFILE_INIT=> 'handleProfileInit',
        );
    }

    public function __construct(ProfileLoader $profileLoader)
    {
        $this->profileLoader = $profileLoader;
        $this->dialogHelper = new DialogHelper;
    }

    public function handleProfileInit(ProfileInitEvent $e)
    {
        $profile = $e->getProfile();
        $input = $e->getInput();
        $output = $e->getOutput();
        $transport = $input->getOption('transport');
        $profileName = $input->getOption('profile');

        // if both transport and profile specified, then user wants
        // to create or update an existing profile
        if ($profileName && $transport) {
            $profile->setName($profileName);
            $overwrite = false;

            if (file_exists($this->profileLoader->getProfilePath($profileName))) {
                $res = $this->dialogHelper->askConfirmation($output, sprintf('Update existing profile "%s"?', $profileName));
                $overwrite = true;
            } else {
                $res = $this->dialogHelper->askConfirmation($output, sprintf('Create new profile "%s"?', $profileName));
            }

            if ($res) {
                $this->profileLoader->saveProfile($profile, $overwrite);
            }
        }
    }
}

