<?php

namespace PHPCR\Shell\Console\Wizard;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPCR\Shell\Transport\TransportFactoryInterface;

class ConnectionWizard implements WizardInterface
{
    protected $dialog;
    protected $transportFactory;

    public function __construct(
        DialogHelper $dialog,
        TransportFactoryInterface $transportFactory
    ) {
        $this->dialog = $dialog;
        $this->transportFactory = $transportFactory;
    }

    /**
     * Run the wizard
     *
     * @return ShellSessionConfig
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $config = array();

        $transportNames = $this->transportFactory->getTransportNames();

        foreach ($transportNames as $i => $transportName) {
            $output->writeln(sprintf('(%d) %s', $i, $transportName));
        }
        $choice = $this->dialog->ask($output, 'Choose a transport');
        $transportName = $transportNames[$choice];
        $transport = $this->transportFactory->getTransport($transportName);
        $templateParameters = $transport->getTemplateConnectionParameters();

        foreach ($templateParameters as $key => $defaultValue) {
            $answer = $this->dialog->ask($output, $key, $defaultValue);
            $config[$key] = $answer;
        }

        return $config;
    }
}
