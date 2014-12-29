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
use Symfony\Component\Console\Helper\QuestionHelper;
use PHPCR\Shell\Config\ProfileLoader;

class ProfileWriterSubscriber implements EventSubscriberInterface
{
    protected $profileLoader;
    protected $questionHelper;

    public static function getSubscribedEvents()
    {
        return array(
            PhpcrShellEvents::PROFILE_INIT => 'handleProfileInit',
        );
    }

    public function __construct(ProfileLoader $profileLoader, $questionHelper)
    {
        $this->profileLoader = $profileLoader;
        $this->questionHelper = $questionHelper;
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
                $res = $this->questionHelper->askConfirmation($output, sprintf('Update existing profile "%s"?', $profileName));
                $overwrite = true;
            } else {
                $res = $this->questionHelper->askConfirmation($output, sprintf('Create new profile "%s"?', $profileName));
            }

            if ($res) {
                $this->profileLoader->saveProfile($profile, $overwrite);
            }
        }
    }
}
