<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepositoryDescriptorListCommand extends BasePhpcrCommand
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
        $session = $this->get('phpcr.session');
        $repository = $session->getRepository();
        $keys = $repository->getDescriptorKeys();

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Key', 'Value', 'Standard?'));

        foreach ($keys as $key) {
            $descriptor = $repository->getDescriptor($key);
            $isStandard = $repository->isStandardDescriptor($key);
            if (is_array($descriptor)) {
                $descriptor = implode(', ', $this->getDescriptorValue($descriptor));
            }
            $table->addRow(array(
                $key,
                $this->getDescriptorValue($descriptor),
                $isStandard ? 'yes' : 'no',
            ));
        }

        $table->render($output);
    }

    private function getDescriptorValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return $value;
    }
}
