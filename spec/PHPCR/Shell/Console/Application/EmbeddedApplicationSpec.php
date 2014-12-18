<?php

namespace spec\PHPCR\Shell\Console\Application;

use PhpSpec\ObjectBehavior;
use PHPCR\Shell\Console\Application\EmbeddedApplication;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmbeddedApplicationSpec extends ObjectBehavior
{
    public function let(
        ContainerInterface $container
    )
    {
        $this->beConstructedWith($container, EmbeddedApplication::MODE_COMMAND);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Application\EmbeddedApplication');
    }
}
