<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Command\Phpcr;

use PHPCR\Shell\Console\Helper\RepositoryHelper;
use PHPCR\Shell\Console\Command\BaseCommand;

/**
 * Base command for all PHPCR action commands
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class BasePhpcrCommand extends BaseCommand
{
    protected $descriptorRequires = array();
    protected $descriptorDequires = array();

    public function requiresDescriptor($descriptorKey, $value = null)
    {
        $this->descriptorRequires[$descriptorKey] = $value;
    }

    public function dequiresDescriptor($descriptorKey, $value = null)
    {
        $this->descriptorDequires[$descriptorKey] = $value;
    }

    public function getDescriptorRequires()
    {
        return $this->descriptorRequires;
    }

    public function getDescriptorDequires()
    {
        return $this->descriptorDequires;
    }

    public function isSupported()
    {
        $repositoryHelper = $this->get('helper.repository');
        foreach ($this->descriptorRequires as $key => $value) {
            $has = $repositoryHelper->hasDescriptor($key, $value);
            if (!$has) {
                return false;
            }
        }

        foreach ($this->descriptorDequires as $key => $value) {
            $has = $repositoryHelper->hasDescriptor($key, $value);

            if ($has) {
                return false;
            }
        }

        return true;
    }

    public function getDescriptor()
    {
        return true;
    }
}
