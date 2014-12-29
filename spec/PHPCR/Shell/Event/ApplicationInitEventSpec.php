<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\PHPCR\Shell\Event;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Application;

class ApplicationInitEventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Event\ApplicationInitEvent');
    }

    public function let(
        Application $application
    )
    {
        $this->beConstructedWith($application);
    }

    public function it_will_return_the_application(
        Application $application
    )
    {
        $this->getApplication()->shouldReturn($application);
    }
}
