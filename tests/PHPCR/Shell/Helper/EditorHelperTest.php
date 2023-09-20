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

use PHPUnit\Framework\TestCase;

class EditorHelperTest extends TestCase
{
    protected $helper;

    public function setUp(): void
    {
        $this->helper = new EditorHelper();
        putenv('EDITOR=cat');
    }

    public function testFromValue()
    {
        $res = $this->helper->fromString(
            <<<'EOT'
One
Two
Three
EOT
        );

        $this->assertEquals(
            <<<'EOT'
One
Two
Three
EOT
            ,
            $res
        );
    }

    public function testFromValueNoEditor()
    {
        $this->expectException(\RuntimeException::class);

        putenv('EDITOR=');
        $res = $this->helper->fromString('asd');
    }

    public function provideFromStringWithMessage()
    {
        return [
            [
                <<<'EOT'
This is some text that I want to edit
EOT
                ,
                <<<'EOT'
This is some text that I want the user to see in a commend

OK
EOT
            ],
        ];
    }

    /**
     * @dataProvider provideFromStringWithMessage
     */
    public function testFromStringWithMessage($source, $message)
    {
        $res = $this->helper->fromStringWithMessage($source, $message);
        $this->assertSame($source, $res);
    }
}
