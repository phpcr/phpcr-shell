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

use PHPCR\Util\PathHelper as StaticPathHelper;
use Symfony\Component\Console\Helper\Helper;

/**
 * Phpcr path helper
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PathHelper extends Helper
{
    /**
     * @see StaticPathHelper::getParentPath
     */
    public function getParentPath($path)
    {
        return StaticPathHelper::getParentPath($path);
    }

    /**
     * @see StaticPathHelper::getNodeName
     */
    public function getNodeName($path)
    {
        return StaticPathHelper::getNodeName($path);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'path';
    }
}
