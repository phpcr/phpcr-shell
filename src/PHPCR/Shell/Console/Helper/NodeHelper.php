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

use Symfony\Component\Console\Helper\Helper;
use PHPCR\NodeInterface;

/**
 * Helper for nodes
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class NodeHelper extends Helper
{
    /**
     * Return true if the given node has the given mixinType
     *
     * @return boolean
     */
    public function nodeHasMixinType($node, $mixinTypeName)
    {
        $mixinTypes = $node->getMixinNodeTypes();

        foreach ($mixinTypes as $mixinType) {
            if ($mixinTypeName == $mixinType->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if the given node is versionable
     */
    public function assertNodeIsVersionable(NodeInterface $node)
    {
        if (!$this->nodeHasMixinType($node, 'mix:versionable')) {
            throw new \OutOfBoundsException(sprintf(
                'Node "%s" is not versionable', $node->getPath()
            ));
        }
    }

    public function getName()
    {
        return 'node';
    }
}
