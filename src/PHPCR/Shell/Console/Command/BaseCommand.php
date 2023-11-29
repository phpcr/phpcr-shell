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

namespace PHPCR\Shell\Console\Command;

use PHPCR\Shell\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null): void
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
