<?php

namespace PHPCR\Shell\Console\Helper;

use PHPCR\RepositoryInterface;
use Symfony\Component\Console\Helper\Helper;

class RepositoryHelper extends Helper
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var array
     */
    protected $descriptors;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return true if the repository supports the given descriptor
     * which relates to a descriptor key
     *
     * @param string $descriptor
     */
    public function hasDescriptor($descriptor, $value = null)
    {
        $this->loadDescriptors();

        $exists = array_key_exists($descriptor, $this->descriptors);

        if (false === $exists) {
            return false;
        }

        if (null === $value) {
            return true;
        }

        $descriptorValue = $this->descriptors[$descriptor];

        // normalize
        if ($descriptorValue === 'true') {
            $descriptorValue = true;
        }
        if ($descriptorValue === 'false') {
            $descriptorValue = false;
        }

        if ($value === $descriptorValue) {
            return true;
        }

        return false;
    }

    private function loadDescriptors()
    {
        if (null === $this->descriptors) {
            foreach ($this->repository->getDescriptorKeys() as $key) {
                $this->descriptors[$key] = $this->repository->getDescriptor($key);
            }
        }
    }

    public function getName()
    {
        return 'repository';
    }
}
