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

namespace PHPCR\Shell\Phpcr;

use PHPCR\PathNotFoundException;
use PHPUnit\Framework\TestCase;

class PhpcrSessionTest extends TestCase
{
    public function setUp(): void
    {
        $this->phpcr = $this->prophesize('PHPCR\SessionInterface');
        $this->session = new PhpcrSession($this->phpcr->reveal());
    }

    public function provideChdir()
    {
        return [
            ['/', '/', '/'],
            ['/', 'cms', '/cms'],
            ['/', '/cms', '/cms'],
            ['/cms', 'foo', '/cms/foo'],
            ['/cms', '..', '/'],
            ['/', '..', '/'],
            ['/cms/foobar/foo', '..', '/cms/foobar'],
        ];
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
        return [
            ['/', '/', '/'],
            ['/', 'cms', '/cms'],
            ['/', '/cms', '/cms'],
            ['/cms', 'foo', '/cms/foo'],
            ['/cms', '', '/cms'],
            ['/cms', null, '/cms'],
            ['/cms', '.', '/cms'],
        ];
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
        return [
            ['/', 'foo', 'bar', '/foo', '/bar'],
        ];
    }

    /**
     * @dataProvider provideMv
     */
    public function testMv($cwd, $relSrc, $relTar, $expSrc, $expTar)
    {
        $this->phpcr->move($expSrc, $expTar)->shouldBeCalled();

        $this->phpcr->getNode('/bar', -1)->willThrow(new PathNotFoundException());
        $this->session->setCwd($cwd);
        $this->session->move($relSrc, $relTar);
    }
}
