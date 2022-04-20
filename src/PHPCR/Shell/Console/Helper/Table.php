<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Table as OriginalTable;

class Table extends OriginalTable
{
    private $nbRows = 0;

    public function addRow($row): static
    {
        $this->nbRows++;

        return parent::addRow($row);
    }

    public function getNumberOfRows()
    {
        return $this->nbRows;
    }
}
