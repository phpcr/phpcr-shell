<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\Helper;
use PHPCR\NodeInterface;

/**
 * Helper for nodes
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class NodeHelper extends Helper
{
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
