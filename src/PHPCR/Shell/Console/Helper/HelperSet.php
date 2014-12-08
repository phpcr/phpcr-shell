<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperSet as BaseHelperSet;

/**
 * Helper for launching external editor
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class HelperSet extends BaseHelperSet
{
    public function get($name)
    {
        $level = error_reporting(0);
        $helper = parent::get($name);
        error_reporting($level);
        return $helper;
    }

}

