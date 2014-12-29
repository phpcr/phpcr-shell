<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Config;

use PhpSpec\ObjectBehavior;

class ProfileSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            'foobar'
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Config\Profile');
    }

    public function it_has_a_method_to_set_config(
    )
    {
        $this->set('transport', array());
    }

    public function it_has_a_method_to_get_config()
    {
        $this->set('transport', array(
            'foo' => 'bar'
        ));

        $this->get('transport')->shouldReturn(array(
            'foo' => 'bar'
        ));

        $this->get('transport', 'foo')->shouldReturn('bar');
    }
}
