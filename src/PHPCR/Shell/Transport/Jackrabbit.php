<?php

namespace PHPCR\Shell\Console\Transport;

use Doctrine\DBAL\DriverManager;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use PHPCR\Shell\Console\TransportInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

class Jackrabbit implements TransportInterface
{
    public function getName()
    {
        return 'jackrabbit';
    }

    public function getRepository()
    {
        throw new \Exception('Not implemented yet');
    }
}

