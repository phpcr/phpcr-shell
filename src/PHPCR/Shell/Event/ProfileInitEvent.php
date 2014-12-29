<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Event;

use PHPCR\Shell\Config\Profile;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Event that is fired when the Profile needs to be initialized.
 *
 * The profile is always created as a new object, event listeners
 * then populate it. (e.g. from a config file, or from CLI params)
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ProfileInitEvent extends Event
{
    protected $profile;
    protected $input;
    protected $output;

    public function __construct(Profile $profile, InputInterface $input, OutputInterface $output)
    {
        $this->profile = $profile;
        $this->input = $input;
        $this->output = $output;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getProfile()
    {
        return $this->profile;
    }
}
