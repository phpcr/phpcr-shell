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

namespace spec\PHPCR\Shell\Console\Application;

use PHPCR\Shell\DependencyInjection\Container;
use PhpSpec\ObjectBehavior;

class EmbeddedApplicationSpec extends ObjectBehavior
{
    public function let(
        Container $container
    ) {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Application\EmbeddedApplication');
    }
}
