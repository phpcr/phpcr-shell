<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use PHPCR\Shell\Console\Helper\RepositoryHelper;

class PhpcrShellCommand extends Command
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

    public function isSupported(RepositoryHelper $repositoryHelper)
    {
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
