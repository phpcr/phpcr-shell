<?php

namespace PHPCR\Shell\Console\Helper;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Helper\Helper;
use PHPCR\Shell\Console\Transport\DoctrineDbal;

class DoctrineDbalHelper extends Helper
{
    protected $connection;
    protected $doctrineDbal;

    public function __construct(DoctrineDbal $doctrineDbal)
    {
        $this->doctrineDbal = $doctrineDbal;
    }

    public function getConnection()
    {
        return $this->doctrineDbal->getConnection();
    }

    public function getName()
    {
        return 'jackalope-doctrine-dbal';
    }
}
