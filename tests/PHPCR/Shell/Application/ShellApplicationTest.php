<?php

namespace PHPCR\Shell\Application;

use PHPCR\Shell\Console\Application\SessionApplication;
use PHPCR\Shell\Console\Application\ShellApplication;

class ShellApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->transport = $this->getMock(
            'PHPCR\Shell\Console\TransportInterface'
        );

        $this->sessionInput = $this->getMock(
            'Symfony\Component\Console\Input\InputInterface'
        );
        $this->sessionInput->expects($this->any())
            ->method('getOption')
            ->will($this->returnCallback(function ($name) {
                $options = array(
                    'transport' => 'test',
                    'phpcr-username' => 'test-username',
                    'phpcr-password' => 'test-password',
                    'phpcr-workspace' => 'test-workspace',
                );

                return $options[$name];
            }));

        $this->session = $this->getMock(
            'PHPCR\SessionInterface'
        );

        $this->repository = $this->getMock(
            'PHPCR\RepositoryInterface'
        );

        $this->application = new ShellApplication('phpcr','v0.test',  $this->sessionInput, array($this->transport));
        $this->application->setSessionInput($this->sessionInput);
        $this->application->setAutoExit(false);
    }

    public function testShellApplication()
    {
        $this->application->run();
    }
}
