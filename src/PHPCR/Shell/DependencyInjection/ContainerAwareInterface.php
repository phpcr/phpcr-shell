<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This was removed from Symfony in version 7.
 *
 * We keep using the pattern in the shell commands for now to avoid a complicated refactor.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ContainerAwareInterface
{
    public function setContainer(?ContainerInterface $container): void;
}
