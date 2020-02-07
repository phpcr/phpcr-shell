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

namespace PHPCR\Shell\Console\Helper;

use PHPCR\NodeInterface;
use PHPUnit\Framework\TestCase;

class NodeHelperTest extends TestCase
{
    protected $nodeHelper;

    public function setUp(): void
    {
        $this->session = $this->prophesize('PHPCR\SessionInterface');
        $this->helper = new NodeHelper($this->session->reveal());
        $this->node = $this->prophesize(NodeInterface::class);

        $this->nodeType1 = $this->prophesize('PHPCR\NodeType\NodeTypeInterface');
    }

    public function provideAssertNodeIsVersionable()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider provideAssertNodeIsVersionable
     */
    public function testAssertNodeIsVersionable($isVersionable)
    {
        $this->node->getPath()->willReturn('/');
        $this->node->isNodeType('mix:versionable')->willReturn($isVersionable)->shouldBeCalled();

        if (false == $isVersionable) {
            $this->expectException('\OutOfBoundsException', 'is not versionable');
        }
        $this->helper->assertNodeIsVersionable($this->node->reveal());
    }
}
