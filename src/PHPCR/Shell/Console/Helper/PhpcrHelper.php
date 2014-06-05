<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

use PHPCR\Shell\Config\Profile;
use PHPCR\Shell\PhpcrSession;
use PHPCR\SimpleCredentials;
use PHPCR\Shell\Transport\TransportRegistryInterface;
use PHPCR\SessionInterface;

/**
 * Helper for managing PHPCR sessions
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrHelper extends Helper
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
     * @param Profile $profile
     */
    public function __construct(TransportRegistryInterface $transportRegistry, Profile $profile)
    {
        $this->transportRegistry = $transportRegistry;
        $this->profile = $profile;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'phpcr';
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
