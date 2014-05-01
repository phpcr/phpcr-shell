<?php

namespace PHPCR\Shell\Transport\Transport;

/**
 * Interface for PHPCR Shell transports
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface TransportInterface
{
    /**
     * Return the name of this transport
     *
     * @return string
     */
    public function getName();

    /**
     * Return the PHPCR repository for this transport
     *
     * @return PHPCR\RepositoryInterface
     */
    public function getRepository();

    /**
     * Return a key => value array of parameters to default values.
     *
     * @return array
     */
    public function getTemplateConnectionParameters();
}
