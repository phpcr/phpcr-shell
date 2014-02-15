<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoryDescriptorListCommand extends Command
{
    protected function configure()
    {
        $this->setName('repository:descriptor:list');
        $this->setDescription('List the descriptors for the current repository');
        $this->setHelp(<<<HERE
Repositories indicate support for the JCR specification via. descriptors. This
command lists all of the descriptor keys and values for the current repository.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->getHelper('phpcr')->getSession();
        $repository = $session->getRepository();
        $keys = $repository->getDescriptorKeys();

        $table = $this->getHelper('table');
        $table->setHeaders(array('Key', 'Value'));

        foreach ($keys as $key) {
            $descriptor = $repository->getDescriptor($key);
            if (is_array($descriptor)) {
                $descriptor = implode(', ', $descriptor);
            }
            $table->addRow(array($key, $descriptor));
        }

        $table->render($output);
    }
}
