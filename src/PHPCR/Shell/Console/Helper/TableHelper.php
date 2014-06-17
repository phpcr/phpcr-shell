<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\TableHelper as OriginalTableHelper;
use Symfony\Component\Console\Helper\Helper;

/**
 * Factory wrapper for Table class.
 *
 * This avoids the user of the "clone" hack, which doesn't work
 * as of Symfony 2.5.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class TableHelper extends Helper
{
    public function create()
    {
        return new OriginalTableHelper();
    }

    public function getName()
    {
        return 'table';
    }
}
