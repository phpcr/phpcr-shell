<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PHPCR\Shell\Transport\Transport;

use Doctrine\DBAL\DriverManager;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use PHPCR\Shell\Config\Config;
use PHPCR\Shell\Transport\TransportInterface;

class DoctrineDbal implements TransportInterface
{
    public function getName()
    {
        return 'doctrine-dbal';
    }

    public function getRepository(Config $config)
    {
        $connection = DriverManager::getConnection($ops = [
            'user'     => $config['db_username'],
            'password' => $config['db_password'],
            'host'     => $config['db_host'],
            'driver'   => $config['db_driver'],
            'dbname'   => $config['db_name'],
            'path'     => $config['db_path'],
        ]);

        $factory = new RepositoryFactoryDoctrineDBAL();
        $repository = $factory->getRepository([
            'jackalope.doctrine_dbal_connection' => $connection,
        ]);

        return $repository;
    }
}
