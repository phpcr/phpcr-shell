<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use PHPCR\SessionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Command\SelectCommand;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;

class ShellApplication extends Application
{
    public function __construct(SessionInterface $session)
    {
        parent::__construct('PHPCR', '1.0');

        $this->add(
            new SelectCommand()
        );

        foreach ($this->all() as $command) {
            if ($command instanceof AbstractSessionCommand) {
                $command->setSession($session);
            }
        }
    }
}
