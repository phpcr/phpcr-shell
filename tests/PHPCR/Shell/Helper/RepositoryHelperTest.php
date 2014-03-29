<?php

namespace PHPCR\Shell\Console\Helper;

use PHPCR\Shell\Console\Helper\NodeHelper;
use PHPCR\NodeInterface;
use PHPCR\Shell\Console\Helper\RepositoryHelper;

class RepositoryHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $nodeHelper;

    public function setUp()
    {
        $this->repository = $this->getMock('PHPCR\RepositoryInterface');
        $this->helper = new RepositoryHelper($this->repository);
    }

    public function testHasDescriptor()
    {
        $descriptors = array(
            'foobar' => true,
            'barfoo' => true,
            'zoobar' => false,
            'true' => 'true',
            'false' => 'false',
        );

        $this->repository->expects($this->once())
            ->method('getDescriptorKeys')
            ->will($this->returnValue(array_keys($descriptors)));

        $this->repository->expectS($this->any())
            ->method('getDescriptor')
            ->will($this->returnCallback(function ($k) use ($descriptors) {
                return $descriptors[$k];
            }));

        $res = $this->helper->hasDescriptor('zoobar', false);
        $this->assertTrue($res);

        $res = $this->helper->hasDescriptor('foobar');
        $this->assertTrue($res);

        $res = $this->helper->hasDescriptor('asdasd');
        $this->assertFalse($res);

        $res = $this->helper->hasDescriptor('');
        $this->assertFalse($res);

        $res = $this->helper->hasDescriptor('foobar', 'asdasd');
        $this->assertFalse($res);

        $res = $this->helper->hasDescriptor('true', true);
        $this->assertTrue($res);

        $res = $this->helper->hasDescriptor('false', false);
        $this->assertTrue($res);
    }
}

