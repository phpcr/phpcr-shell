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
use PHPCR\Util\QOM\Sql2ToQomQueryConverter;

class BaseQueryCommand extends BasePhpcrCommand
{
    public function getQuery(InputInterface $input)
    {
        $sql = $input->getRawCommand();

        if (substr($sql, -1) !== ';') {
            throw new \InvalidArgumentException(
                'Queries must be terminated with ";"'
            );
        }

        return substr($sql, 0, -1);
    }
}
