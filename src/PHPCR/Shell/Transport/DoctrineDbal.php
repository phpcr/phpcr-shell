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
            'user' => $this->input->getOption('db_username'),
            'password' => $this->input->getOption('db_password'),
            'host' => $this->input->getOption('db_host'),
            'driver' => $this->input->getOption('db_driver'),
            'dbname' => $this->input->getOption('db_name'),
            'path' => $this->input->getOption('db_path'),
        ));

        $factory = new RepositoryFactoryDoctrineDBAL();
        $repository = $factory->getRepository(array(
            'jackalope.doctrine_dbal_connection' => $connection
        ));

        return $repository;
    }
}
