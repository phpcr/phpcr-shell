<?php

namespace PHPCR\Shell\Console\Command\Phpcr;

use Jackalope\Tools\Console\Command\InitDoctrineDbalCommand;

class DoctrineDbalInitCommand extends InitDoctrineDbalCommand
{
    public function configure()
    {
        parent::configure();
        $this->setName('dbal-init');
    }
}
