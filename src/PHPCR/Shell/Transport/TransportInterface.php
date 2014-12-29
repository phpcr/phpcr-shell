<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Transport;

/**
 * All phpcr-shell transports must implement this interface
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface TransportInterface
{
    public function getName();

    public function getRepository(array $config);
}
