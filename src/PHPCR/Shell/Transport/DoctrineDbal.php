<?php

namespace PHPCR\Shell\Transport;

use Doctrine\DBAL\DriverManager;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use PHPCR\Shell\Console\TransportInterface;
use PHPCR\SimpleCredentials;
use Symfony\Component\Console\Application as BaseApplication;
use Jackalope\Tools\Console\Helper\DoctrineDbalHelper;
use PHPCR\Shell\Console\Command\DoctrineDbalInitCommand;
use Symfony\Component\Console\Input\InputInterface;

class DoctrineDbal implements TransportInterface
{
    protected $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    public function getName()
    {
        return 'doctrine-dbal';
    }

    public function getRepository()
    {
        $connection = DriverManager::getConnection($ops = array(
            'user' => $this->input->getOption('db-username'),
            'password' => $this->input->getOption('db-password'),
            'host' => $this->input->getOption('db-host'),
            'driver' => $this->input->getOption('db-driver'),
            'dbname' => $this->input->getOption('db-name'),
            'path' => $this->input->getOption('db-path'),
        ));

        $factory = new RepositoryFactoryDoctrineDBAL();
        $repository = $factory->getRepository(array(
            'jackalope.doctrine_dbal_connection' => $connection
        ));

        return $repository;
    }
}
