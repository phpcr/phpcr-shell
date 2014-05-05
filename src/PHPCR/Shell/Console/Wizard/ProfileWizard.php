<?php

namespace PHPCR\Shell\Console\Wizard;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use PHPCR\Shell\Console\Helper\ProfileHelper;
use PHPCR\Shell\Config\Profile;

class ProfileWizard implements WizardInterface
{
    protected $dialog;
    protected $profileHelper;
    protected $connectionWizard;

    public function __construct(
        DialogHelper $dialog,
        ProfileHelper $profileHelper,
        WizardInterface $connectionWizard
    ) {
        $this->dialog = $dialog;
        $this->profileHelper = $profileHelper;
        $this->connectionWizard = $connectionWizard;
    }

    /**
     * Run the wizard
     *
     * @return ShellSessionConfig
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $profileNames = $this->profileHelper->getProfileNames();

        foreach ($profileNames as $i => $profileName) {
            $output->writeln(sprintf('(%d) Use: %s', $i, $profileName));
        }

        $output->writeln(sprintf('(c) Create a new profile'));

        $selection = $this->dialog->ask($output, 'Enter a choice: ');

        if ($selection === 'c') {
            $profileName = $this->dialog->ask($output, 'Enter name for new profile: ');
            $profile = $this->profileHelper->createNewProfile($profileName);
            $transportConfig = $this->connectionWizard->run($input, $output);
            $profile->set('transport', $transportConfig);
        } else {
            $profile = $this->profileHelper->getProfile($profileNames[$selection]);
        }

        return $profile;
    }
}
