<?php

namespace PHPCR\Shell\Console\Helper;

use PHPCR\Shell\PhpcrSession;

/**
 * Helper for managing PHPCR sessions
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrHelper extends Helper
{
    /**
     * Initial input which was used to initialize the shell.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $sessionInput;

    /**
     * Active PHPCR session
     *
     * @var \PHPCR\SessionInterface
     */
    protected $session;

    /**
     * Available transports
     *
     * @var \Jackalope\Transport\TransportInterface[]
     */
    protected $transports = array();

    /**
     * @param InputInterface $sessionInput
     */
    public function __construct(InputInterface $sessionInput)
    {
        $this->sessionInput = $sessionInput;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'phpcr';
    }

    /**
     * Initialize the PHPCR session
     */
    private function initSession()
    {
        $transport = $this->getTransport();
        $repository = $transport->getRepository();
        $credentials = new SimpleCredentials(
            $this->sessionInput->getOption('phpcr-username'),
            $this->sessionInput->getOption('phpcr-password')
        );

        $session = $repository->login($credentials, $this->sessionInput->getOption('phpcr-workspace'));

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
        $this->session->logout();
        $this->sessionInput->setOption('phpcr-workspace', $workspaceName);
        $this->initSession($this->sessionInput);
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
        $this->session->logout();
        $this->sessionInput->setOption('phpcr-username', $username);
        $this->sessionInput->setOption('phpcr-password', $password);

        if ($workspaceName) {
            $this->sessionInput->setOption('phpcr-workspace', $workspaceName);
        }
        $this->initSession($this->sessionInput);
    }

    /**
     * Return the transport as defined in the sessionInput
     */
    private function getTransport()
    {
        $transportName = $this->sessionInput->getOption('transport');

        if (!isset($this->transports[$transportName])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown transport "%s", I have "%s"',
                $transportName, implode(', ', array_keys($this->transports))
            ));
        }

        $transport = $this->transports[$transportName];

        return $transport;
    }

    /**
     * Initialize the supported transports.
     *
     * @todo Do this in a lazy way
     */
    private function initializeTransports()
    {
        $transports = array(
            new \PHPCR\Shell\Transport\DoctrineDbal($this->sessionInput),
            new \PHPCR\Shell\Transport\Jackrabbit($this->sessionInput),
        );

        foreach ($transports as $transport) {
            $this->transports[$transport->getName()] = $transport;;
        }
    }
}
