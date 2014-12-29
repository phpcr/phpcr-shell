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
use PHPCR\RepositoryInterface;
use PHPCR\Shell\Phpcr\SessionManager;

class RepositoryHelperSpec extends ObjectBehavior
{
    public function let(
        SessionManager $sessionManager
    ) {
        $this->beConstructedWith($sessionManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Helper\RepositoryHelper');
    }

    public function it_provides_a_method_to_say_if_a_descriptor_exists_or_not(
        SessionManager $sessionManager,
        RepositoryInterface $repository
    ) {
        $sessionManager->getRepository()->willReturn($repository);
        $repository->getDescriptorKeys()->willReturn(array(
            'foo', 'bar'
        ));
        $repository->getDescriptor('foo')->willReturn('foo');
        $repository->getDescriptor('bar')->willReturn('foo');

        $this->hasDescriptor('foo')->shouldReturn(true);
    }
}
