<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
