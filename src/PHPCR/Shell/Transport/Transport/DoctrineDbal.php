<?php

namespace PHPCR\Shell\Transport\Transport;

use Doctrine\DBAL\DriverManager;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use Symfony\Component\Console\Input\InputInterface;
use PHPCR\Shell\Transport\TransportInterface;

class DoctrineDbal implements TransportInterface
{
    public function getName()
    {
        return 'doctrine-dbal';
    }

    public function getRepository(array $config)
    {
        $connection = DriverManager::getConnection($ops = array(
            'user' => $config['db_username'],
            'password' => $config['db_password'],
            'host' => $config['db_host'],
            'driver' => $config['db_driver'],
            'dbname' => $config['db_name'],
            'path' => $config['db_path'],
        ));

        $factory = new RepositoryFactoryDoctrineDBAL();
        $repository = $factory->getRepository(array(
            'jackalope.doctrine_dbal_connection' => $connection
        ));

        return $repository;
    }
}
