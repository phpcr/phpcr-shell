<?php

namespace PHPCR\Shell\Console\Helper;

use PHPCR\Shell\Console\Helper\EditorHelper;

class EditorHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    public function setUp()
    {
        $this->helper = new EditorHelper();
        putenv('EDITOR=cat');
    }

    public function testFromValue()
    {
        $res = $this->helper->fromString(<<<EOT
One
Two
Three
EOT
        );

        $this->assertEquals(<<<EOT
One
Two
Three
EOT
        , $res
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFromValueNoEditor()
    {
        putenv('EDITOR=');
        $res = $this->helper->fromString('asd');
    }

    public function provideFromStringWithMessage()
    {
        return array(
            array(
                <<<EOT
This is some text that I want to edit
EOT
            , 
                <<<EOT
This is some text that I want the user to see in a commend

OK
EOT
            ),
        );
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
