<?php

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
