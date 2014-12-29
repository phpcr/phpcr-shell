<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPCR\Shell\Console\Command\BaseCommand;

class PathShowCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('shell:path:show');
        $this->setDescription('Print Working Directory (or path)');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>' . $this->get('phpcr.session')->getCwd() . '</comment>'
        );
    }
}
