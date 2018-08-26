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

namespace spec\PHPCR\Shell\Console\Helper;

use PHPCR\NodeInterface;
use PhpSpec\ObjectBehavior;

class NodeHelperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\NodeHelper');
    }

    public function it_should_provide_a_method_to_determine_if_a_node_has_a_given_mixin(
        NodeInterface $node
    ) {
        $node->isNodeType('mixin1')->willReturn(true);
        $node->isNodeType('mixin2')->willReturn(false);
    }

    public function it_should_provide_a_method_to_determine_if_a_node_is_versionable(
        NodeInterface $nodeVersionable,
        NodeInterface $nodeNotVersionable
    ) {
        $nodeNotVersionable->getPath()->willReturn('foobar');
        $nodeVersionable->isNodeType('mix:versionable')->willReturn(true);
        $nodeNotVersionable->isNodeType('mix:versionable')->willReturn(false);
        $this->assertNodeIsVersionable($nodeVersionable)->shouldReturn(null);

        try {
            $this->assertNodeIsVersionable($nodeNotVersionable);
        } catch (\OutOfBoundsException $e) {
        }
    }
}
