<?php

namespace spec\PHPCR\Shell\Console\Application;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPCR\Shell\Console\Application\EmbeddedApplication;
use PHPCR\Shell\PhpcrShell;

class ShellApplicationSpec extends ObjectBehavior
{
    public function let(
        ContainerInterface $container
    )
    {
        $this->beConstructedWith($container, PhpcrShell::MODE_EMBEDDED_COMMAND);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PHPCR\Shell\Console\Application\ShellApplication');
    }
}
