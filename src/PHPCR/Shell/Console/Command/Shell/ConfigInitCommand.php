<?php

namespace PHPCR\Shell\Console\Command\Shell;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigInitCommand extends Command
{
    protected $output;

    public function configure()
    {
        $this->setName('shell:config:init');
        $this->setDescription('Initialize a local configuration with default values');
        $this->setHelp(<<<EOT
Initialize a new configuration folder, <info>.phpcrsh</info> in the users HOME directory.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->configHelper = $this->getHelper('config');

        $fs = new Filesystem();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output->writeln('<error>This feature is currently only supported on Linux and OSX (maybe). Please submit a PR to support it on windows.</error>');
            return 1;
        }

        $configDir = $this->configHelper->getConfigDir();
        $distDir = __DIR__ . '/../../../Resources/config.dist';

        if (!file_exists($configDir)) {
            $this->logCreation($configDir);
            $fs->mkdir($configDir);
        }

        $configFilenames = array(
            'alias.yml',
        );

        foreach ($configFilenames as $configFilename) {
            $srcFile = $distDir . '/' . $configFilename;
            $destFile = $configDir . '/' . $configFilename;

            if (!file_exists($srcFile)) {
                throw new \Exception('Dist (source) file "' . $srcFile . '" does not exist.');
            }

            $fs->copy($srcFile, $destFile);
            $this->logCreation($destFile);
        }
    }

    private function logCreation($path)
    {
        $this->output->writeln('<info>[+]</info> ' . $path);
    }
}
