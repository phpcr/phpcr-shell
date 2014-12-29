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

class SessionInfoCommand extends BasePhpcrCommand
{
    protected function configure()
    {
        $this->setName('session:info');
        $this->setDescription('Display information about current session');
        $this->setHelp(<<<HERE
The command shows some basic information about the current session.
HERE
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $this->get('phpcr.session');
        $repository = $session->getRepository();

        $info = array(
            'user id' => $session->getUserID(),
            'live' => $session->isLive() ? 'yes' : 'no',
            'workspace name' => $session->getWorkspace()->getName(),
            'jcr repository name' => $repository->getDescriptor('jcr.repository.name'),
            'jcr repository vendor' => $repository->getDescriptor('jcr.repository.vendor'),
            'jcr repository version' => $repository->getDescriptor('jcr.repository.version'),
        );

        foreach ($session->getAttributeNames() as $attributeName) {
            $attribute = $session->getAttribute($attributeName);
        }

        $table = $this->get('helper.table')->create();
        $table->setHeaders(array('Key', 'Value'));

        foreach ($info as $key => $value) {
            $table->addRow(array($key, $value));
        }

        $table->render($output);
    }
}
