<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResultFormatterHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\ResultFormatterHelper');
    }

    function it_should_indent_a_given_xml_string()
    {
        $xmlString = <<<EOT
<xml><thisis><foobar></foobar></thisis></xml>
EOT
        ;
        
        $this->formatXml($xmlString)->shouldReturn(<<<EOT
<?xml version="1.0"?>
<xml>
  <thisis>
    <foobar/>
  </thisis>
</xml>

EOT
        );
    }
}
