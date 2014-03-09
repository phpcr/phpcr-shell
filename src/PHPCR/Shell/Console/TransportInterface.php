<?php

namespace PHPCR\Shell\Console;

interface TransportInterface
{
    public function getName();

    public function getRepository();
}
