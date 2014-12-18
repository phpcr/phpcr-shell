<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use PHPCR\Shell\Phpcr\SessionManager;
use PHPCR\SessionInterface;
use PHPCR\Shell\Query\PhpcrRepository;

/**
 * @deprecated
 *
 * This helper is deprecated and only exists to provide a backwards compatible
 * API fo rsetting the PHPCR session as used in the DoctrinePHPCRBundle.
 *
 * It has since been replaced by the SessionManager service.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrHelper extends Helper
{
    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function setSession(SessionInterface $session)
    {
        $this->sessionManager->setSession($session);
    }

    public function getName()
    {
        return 'phpcr';
    }
}
