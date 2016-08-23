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

namespace spec\PHPCR\Shell\Config;

use PhpSpec\ObjectBehavior;

class ConfigSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Config\Config');
    }

    public function let()
    {
        $this->beConstructedWith([
            'foo' => 'bar',
            'bar' => [
                'boo' => 'baz',
            ],
        ]);
    }

    public function it_should_be_able_to_access_data_values()
    {
        $this['foo']->shouldReturn('bar');
    }

    public function it_should_be_able_to_access_nested_config()
    {
        $this['bar']['boo']->shouldReturn('baz');
    }
}
