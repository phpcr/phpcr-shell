<?php

namespace PHPCR\Shell\Transport;

use Doctrine\DBAL\DriverManager;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use PHPCR\Shell\Console\TransportInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Jackalope\RepositoryFactoryJackrabbit;

class Jackrabbit implements TransportInterface
{
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    public function getName()
    {
        return 'jackrabbit';
    }

    public function getRepository()
    {
        $params = array(
            'jackalope.jackrabbit_uri'  => $this->input->getOption('repo-url'),
        );
        $factory = new RepositoryFactoryJackrabbit();
        $repository = $factory->getRepository($params);

        return $repository;
    }
}

