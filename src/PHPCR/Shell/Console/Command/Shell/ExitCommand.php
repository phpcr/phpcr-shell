<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExitCommand extends Command
{
    public function configure()
    {
        $this->setName('shell:exit');
        $this->setDescription('Logout and quit the shell');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $session = $this->getHelper('phpcr')->getSession();
        $noInteraction = $input->getOption('no-interaction');

        if ($session->hasPendingChanges()) {
            $res = false;

            if ($input->isInteractive()) {
                $res = $dialog->askConfirmation($output, '<question>Session has pending changes, are you sure you want to quit? (Y/N)</question>', false);
            }

            if (false === $res) {
                return;
            }
        }

        $session->logout();
        $output->writeln('<info>Bye!</info>');
        exit(0);
    }
}
