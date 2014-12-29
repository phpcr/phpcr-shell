<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Console\Helper;

use PhpSpec\ObjectBehavior;

class PathHelperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\PathHelper');
    }

    public function it_has_a_method_to_provide_the_parent_path_for_a_given_path()
    {
        $this->getParentPath('/foo/bar')->shouldReturn('/foo');
    }

    public function it_has_a_method_to_get_the_node_name_of_a_given_path()
    {
        $this->getNodeName('/foo/bar')->shouldReturn('bar');
    }
}
