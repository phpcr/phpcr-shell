<?php

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PathHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\PathHelper');
    }

    function it_has_a_method_to_provide_the_parent_path_for_a_given_path()
    {
        $this->getParentPath('/foo/bar')->shouldReturn('/foo');
    }

    function it_has_a_method_to_get_the_node_name_of_a_given_path()
    {
        $this->getNodeName('/foo/bar')->shouldReturn('bar');
    }
}
