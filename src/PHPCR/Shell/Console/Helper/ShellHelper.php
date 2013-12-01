<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use PHPCR\SimpleCredentials;
use PHPCR\Shell\Console\TransportInterface;
use Symfony\Component\Console\Input\InputInterface;

class ShellHelper extends Helper
{
    protected $transport;
    protected $input;

    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    public function getSession()
    {
        $repository = $this->transport->getRepository();

        $credentials = new SimpleCredentials(
            $this->input->getOption('phpcr_username'),
            $this->input->getOption('phpcr_password')
        );

        $session = $repository->login($credentials, $this->input->getOption('phpcr_workspace'));

        return $session;
    }

    public function getName()
    {
        return 'shell';
    }
}
