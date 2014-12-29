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

use Jackalope\Tools\Console\Command\InitDoctrineDbalCommand;

class DoctrineDbalInitCommand extends InitDoctrineDbalCommand
{
    public function configure()
    {
        parent::configure();
        $this->setName('dbal-init');
    }
}
