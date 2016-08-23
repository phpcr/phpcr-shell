<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Table as OriginalTable;

class Table extends OriginalTable
{
    private $nbRows = 0;

    public function addRow($row)
    {
        $this->nbRows++;
        parent::addRow($row);
    }

    public function getNumberOfRows()
    {
        return $this->nbRows;
    }
}
