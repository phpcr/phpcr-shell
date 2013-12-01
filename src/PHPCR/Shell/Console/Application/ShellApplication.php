<?php

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use PHPCR\SessionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Command\AbstractSessionCommand;
use PHPCR\Shell\Console\Command\Workspace\SelectCommand;
use PHPCR\Shell\Console\Command\Workspace\NodeTypeListCommand;
use PHPCR\Util\Console\Helper\PhpcrConsoleDumperHelper;
use PHPCR\Shell\Console\Command\Workspace\NodeDumpCommand;

class ShellApplication extends Application
{
    public function __construct(SessionInterface $session)
    {
        parent::__construct('PHPCR', '1.0');

        $this->add(new SelectCommand());
        $this->add(new NodeTypeListCommand());
        $this->add(new NodeDumpCommand());

        $this->getHelperSet()->set(new PhpcrConsoleDumperHelper());

        foreach ($this->all() as $command) {
            if ($command instanceof AbstractSessionCommand) {
                $command->setSession($session);
            }
        }
    }
}
