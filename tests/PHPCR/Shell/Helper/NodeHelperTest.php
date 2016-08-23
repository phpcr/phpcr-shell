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

use PHPCR\NodeInterface;

class NodeHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeHelper;

    public function setUp()
    {
        $this->session = $this->prophesize('PHPCR\SessionInterface');
        $this->helper = new NodeHelper($this->session->reveal());
        $this->node = $this->prophesize(NodeInterface::class);

        $this->nodeType1 = $this->prophesize('PHPCR\NodeType\NodeTypeInterface');
    }

    public function provideAssertNodeIsVersionable()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider provideAssertNodeIsVersionable
     */
    public function testAssertNodeIsVersionable($isVersionable)
    {
        $this->node->getMixinNodeTypes()->willReturn([
            $this->nodeType1->reveal()
        ]);
        $this->node->getPath()->willReturn('/');

        $nodeTypeName = $isVersionable ? 'mix:versionable' : 'nt:foobar';

        $this->nodeType1->getName()->willReturn($nodeTypeName);

        if (false == $isVersionable) {
            $this->setExpectedException('\OutOfBoundsException', 'is not versionable');
        }
        $this->helper->assertNodeIsVersionable($this->node->reveal());
    }
}
