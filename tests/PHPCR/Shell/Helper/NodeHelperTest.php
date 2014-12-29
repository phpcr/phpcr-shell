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

class NodeHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeHelper;

    public function setUp()
    {
        $this->session = $this->getMock('PHPCR\SessionInterface');
        $this->helper = new NodeHelper($this->session);
        $this->node = $this->getMockBuilder('Jackalope\Node')
            ->disableOriginalConstructor()->getMock();

        $this->nodeType1 = $this->getMock('PHPCR\NodeType\NodeTypeInterface');
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
        $this->node->expects($this->once())
            ->method('getMixinNodeTypes')
            ->will($this->returnValue(array(
                $this->nodeType1,
            )));

        $nodeTypeName = $isVersionable ? 'mix:versionable' : 'nt:foobar';

        $this->nodeType1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($nodeTypeName));

        if (false == $isVersionable) {
            $this->setExpectedException('\OutOfBoundsException', 'is not versionable');
        }
        $this->helper->assertNodeIsVersionable($this->node);
    }
}
