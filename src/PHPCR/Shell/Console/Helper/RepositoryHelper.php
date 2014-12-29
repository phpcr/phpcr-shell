<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use PHPCR\Shell\Phpcr\SessionManager;

class RepositoryHelper extends Helper
{
    /**
     * @var PhpcrHelper
     */
    protected $sessionManager;

    /**
     * @var array
     */
    protected $descriptors;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Return true if the sessionManager supports the given descriptor
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
            $repository = $this->sessionManager->getRepository();

            foreach ($repository->getDescriptorKeys() as $key) {
                $this->descriptors[$key] = $repository->getDescriptor($key);
            }
        }
    }

    public function getName()
    {
        return 'repository';
    }
}
