<?php

namespace PHPCR\Shell\Transport\Transport;

use PHPCR\Shell\Transport\TransportInterface;
use Jackalope\RepositoryFactoryFilesystem;

class JackalopeFs implements TransportInterface
{
    public function getName()
    {
        return 'jackalope-fs';
    }

    public function getRepository(array $config)
    {
        $params = array(
            'path'  => $config['repo_path'],
        );
        $factory = new RepositoryFactoryFilesystem();
        $repository = $factory->getRepository($params);

        return $repository;
    }
}
