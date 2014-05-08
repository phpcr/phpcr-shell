<?php

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
