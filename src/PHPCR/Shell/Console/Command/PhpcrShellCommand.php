<?php

namespace PHPCR\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;

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

    public function isEnabled()
    {
        foreach ($this->descriptorRequires as $key => $value) {
            $has = $this->getHelper('repository')->hasDescriptor($key, $value);
            if (!$has) {
                return false;
            }
        }

        foreach ($this->descriptorDequires as $key => $value) {
            $has = $this->getHelper('repository')->hasDescriptor($key, $value);

            if ($has) {
                return false;
            }
        }

        return true;
    }
}
