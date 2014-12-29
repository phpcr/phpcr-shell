<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Phpcr;

use PHPCR\Shell\Config\Profile;
use PHPCR\SimpleCredentials;
use PHPCR\Shell\Transport\TransportRegistryInterface;
use PHPCR\SessionInterface;

/**
 * PHPCR Session Manager
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SessionManager
{
    /**
     * Active PHPCR session
     *
     * @var \PHPCR\SessionInterface
     */
    protected $session;

    /**
     * The transport registry
     *
     * @var TransportRegistryInterface
     */
    protected $transportRegistry;

    /**
     * @param TransportRegistryInterface $transportRegistry
     * @param Profile                    $profile
     */
    public function __construct(TransportRegistryInterface $transportRegistry, Profile $profile)
    {
        $this->transportRegistry = $transportRegistry;
        $this->profile = $profile;
    }

    private function init()
    {
        if (null === $this->session) {
            $this->initSession();
        }
    }

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Initialize the PHPCR session
     *
     * @access private
     */
    private function initSession()
    {
        $transport = $this->transportRegistry->getTransport($this->profile->get('transport', 'name'));
        $repository = $transport->getRepository($this->profile->get('transport'));

        $credentials = new SimpleCredentials(
            $this->profile->get('phpcr', 'username'),
            $this->profile->get('phpcr', 'password')
        );

        $session = $repository->login($credentials, $this->profile->get('phpcr', 'workspace'));

        // if you are wondering wtf here -- we wrap the PhpcrSession
        if (!$this->session) {
            $this->session = new PhpcrSession($session);
        } else {
            $this->session->setPhpcrSession($session);
        }
    }

    /**
     * Change the current workspace
     *
     * @param string $workspaceName
     */
    public function changeWorkspace($workspaceName)
    {
        $this->init();
        $this->session->logout();
        $this->profile->set('phpcr', 'workspace', $workspaceName);
        $this->initSession($this->profile);
    }

    /**
     * Login (again)
     *
     * @param string $username
     * @param string $password
     * @param string $workspaceName
     */
    public function relogin($username, $password, $workspaceName = null)
    {
        if ($this->session) {
            $this->session->logout();
        }

        $this->profile->set('phpcr', 'username', $username);
        $this->profile->set('phpcr', 'password', $password);

        if ($workspaceName) {
            $this->profile->set('phpcr', 'workspace', $workspaceName);
        }

        $this->init();
    }

    /**
     * Return the current PHPCR session. We lazy call
     * initialize.
     *
     * @return \PHPCR\SessionInterface
     */
    public function getSession()
    {
        $this->init();

        return $this->session;
    }

    /**
     * Proxy for getting the repository (make mocking easier)
     *
     * @return \PHPCR\RepositoryInterface
     */
    public function getRepository()
    {
        $this->init();

        return $this->session->getRepository();
    }
}
