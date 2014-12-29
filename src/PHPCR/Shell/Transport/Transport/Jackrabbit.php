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

use Jackalope\RepositoryFactoryJackrabbit;
use PHPCR\Shell\Transport\TransportInterface;

class Jackrabbit implements TransportInterface
{
    public function getName()
    {
        return 'jackrabbit';
    }

    public function getRepository(array $config)
    {
        $params = array(
            'jackalope.jackrabbit_uri'  => $config['repo_url'],
        );
        $factory = new RepositoryFactoryJackrabbit();
        $repository = $factory->getRepository($params);

        return $repository;
    }
}
