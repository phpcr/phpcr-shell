<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseCommand extends Command implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function get($serviceId)
    {
        if (null === $this->container) {
            throw new \RuntimeException(
                'Container has not been set on this command'
            );
        }

        return $this->container->get($serviceId);
    }
}
