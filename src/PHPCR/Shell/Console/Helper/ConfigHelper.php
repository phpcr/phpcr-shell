<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * Helper for config stuff
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ConfigHelper extends Helper
{
    /**
     * Base filenames of all the possible configuration files
     * in the users configuration directory.
     *
     * @var array
     */
    protected $configKeys = array(
        'alias'
    );

    /**
     * Cached configuration
     *
     * @var array
     */
    protected $cachedConfig = null;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'config';
    }

    /**
     * Return the configuration directory
     *
     * @return string
     */
    public function getConfigDir()
    {
        $home = getenv('PHPCRSH_HOME');

        if ($home) {
            return $home;
        }

        // handle windows ..
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            if (!getenv('APPDATA')) {
                throw new \RuntimeException(
                    'The APPDATA or phpcrsh_HOME environment variable must be set for phpcrsh to run correctly'
                );
            }
            $home = strtr(getenv('APPDATA'), '\\', '/').'/phpcrsh';

            return $home;
        }

        if (!getenv('HOME')) {
            throw new \RuntimeException(
                'The HOME or phpcrsh_HOME environment variable must be set for phpcrsh to run correctly'
            );
        }

        $home = rtrim(getenv('HOME'), '/').'/.phpcrsh';

        return $home;
    }

    /**
     * Load the configuration
     */
    public function loadConfig()
    {
        $config = array();

        $configDir = $this->getConfigDir();

        foreach ($this->configKeys as $configKey) {
            $fullPath = $configDir . '/' . $configKey . '.yml';
            $config[$configKey] = array();

            if (file_exists($fullPath)) {
                $config[$configKey] = Yaml::parse($fullPath);
            }
        }

        $this->cachedConfig = $config;

        return $config;
    }

    /**
     * Return the configuration
     *
     * @return array
     */
    public function getConfig($type)
    {
        if (null !== $this->cachedConfig) {
            return $this->cachedConfig[$type];
        }

        $this->loadConfig();
        return $this->cachedConfig[$type];
    }

    /**
     * Initialize a configuration files
     */
    public function initConfig(OutputInterface $output = null, DialogHelper $dialogHelper = null)
    {
        $log = function ($message) use ($output) {
            if ($output) {
                $output->writeln($message);
            }
        };

        $fs = new Filesystem();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            throw new \RuntimeException('This feature is currently only supported on Linux and OSX (maybe). Please submit a PR to support it on windows.');
        }

        $configDir = $this->getConfigDir();
        $distDir = __DIR__ . '/../../Resources/config.dist';

        if (!file_exists($configDir)) {
            $log('<info>[+] Creating directory:</info> ' . $configDir);
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

            if (file_exists($destFile)) {
                if (null !== $dialogHelper) {
                    if (!$dialogHelper->askConfirmation($output, '"' . $configFilename . '" already exists, do you want to overwrite it?')) {
                        return 0;
                    }
                } else {
                    $log(sprintf('<info>File</info> %s <info> already exists, not overwriting.', $destFile));
                }
            }

            $fs->copy($srcFile, $destFile);
            $log('<info>[+] Creating file:</info> ' . $destFile);
        }
    }
}
