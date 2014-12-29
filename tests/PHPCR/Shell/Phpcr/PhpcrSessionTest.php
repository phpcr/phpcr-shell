<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Phpcr;

use PHPCR\SessionInterface;
use PHPCR\PathNotFoundException;

class PhpcrSessionTest extends \Phpunit_Framework_TestCase
{
    public function setUp()
    {
        $this->phpcr = $this->getMock('PHPCR\SessionInterface');
        $this->session = new PhpcrSession($this->phpcr);
    }

    public function provideChdir()
    {
        return array(
            array('/', '/', '/'),
            array('/', 'cms', '/cms'),
            array('/', '/cms', '/cms'),
            array('/cms', 'foo', '/cms/foo'),
            array('/cms', '..', '/'),
            array('/', '..', '/'),
            array('/cms/foobar/foo', '..', '/cms/foobar'),
        );
    }

    /**
     * @dataProvider provideChdir
     */
    public function testChdir($cwd, $path, $expected)
    {
        $this->session->setCwd($cwd);
        $this->session->chdir($path);
        $this->assertEquals($expected, $this->session->getCwd());
    }

    public function provideAbsPath()
    {
        return array(
            array('/', '/', '/'),
            array('/', 'cms', '/cms'),
            array('/', '/cms', '/cms'),
            array('/cms', 'foo', '/cms/foo'),
            array('/cms', '', '/cms'),
            array('/cms', null, '/cms'),
            array('/cms', '.', '/cms'),
        );
    }

    /**
     * @dataProvider provideAbsPath
     */
    public function testAbsPath($cwd, $path, $expected)
    {
        $this->session->setCwd($cwd);
        $absPath = $this->session->getAbsPath($path);
        $this->assertEquals($expected, $absPath);
    }

    public function provideMv()
    {
        return array(
            array('/', 'foo', 'bar', '/foo', '/bar')
        );
    }

    /**
     * @dataProvider provideMv
     */
    public function testMv($cwd, $relSrc, $relTar, $expSrc, $expTar)
    {
        $this->phpcr->expects($this->once())
            ->method('move')
            ->with($expSrc, $expTar);
        $this->phpcr->expects($this->once())
            ->method('getNode')
            ->will($this->throwException(new PathNotFoundException()));
        $this->session->setCwd($cwd);
        $this->session->move($relSrc, $relTar);
    }
}
