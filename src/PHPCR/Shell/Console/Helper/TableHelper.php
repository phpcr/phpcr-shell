<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\TableHelper as OriginalTableHelper;

/**
 * Factory wrapper for Table class.
 *
 * This avoids the user of the "clone" hack, which doesn't work
 * as of Symfony 2.5.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class TableHelper extends OriginalTableHelper
{
    private $numberOfRows = 0;

    public function __construct()
    {
        parent::__construct(false);
    }

    public function create()
    {
        return new self(false);
    }

    public function addRow(array $row)
    {
        parent::addRow($row);
        $this->numberOfRows++;
    }

    public function getNumberOfRows()
    {
        return $this->numberOfRows;
    }
}
