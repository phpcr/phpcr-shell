<?php

namespace PHPCR\Shell\Console;

use Symfony\Component\Console\Input\InputInterface;

interface TransportInterface
{
    public function getName();

    public function getRepository();
}
