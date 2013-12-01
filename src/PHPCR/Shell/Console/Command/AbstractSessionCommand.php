<?php

namespace PHPCR\Shell\Console\Command;

use PHPCR\SessionInterface;
use Symfony\Component\Console\Command\Command;

class AbstractSessionCommand extends Command
{
    protected $session;

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        if (!$this->session) {
            throw new \InvalidArgumentException('Session has not been set.');
        }
        return $this->session;
    }
}
