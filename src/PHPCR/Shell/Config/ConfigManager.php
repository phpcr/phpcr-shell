<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Config;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Configuration manager
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ConfigManager
{
    /**
     * Base filenames of all the possible configuration files
     * in the users configuration directory.
     *
     * @var array
     */
    protected $configKeys = array(
        'alias',
        'phpcrsh',
    );

    /**
     * Cached configuration
     *
     * @var array
     */
    protected $cachedConfig = null;

    /**
     * Filesystem
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var QuestionHelper|DialogHelper
     */
    protected $questionHelper;

    /**
     * Constuctor - can optionally accept a Filesystem object
     * for testing purposes, otherwise one is created.
     *
     * @param QuestionHelper|DialogHelper $questionHelper
     * @param Filesystem                  $filesystem
     */
    public function __construct($questionHelper, Filesystem $filesystem = null)
    {
        if (null === $filesystem) {
            $filesystem = new Filesystem();
        }

        $this->filesystem = $filesystem;
        $this->questionHelper = $questionHelper;
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

    private function getDistConfigDir()
    {
        return __DIR__ . '/../Resources/config.dist';
    }

    /**
     * Load the configuration
     */
    public function loadConfig()
    {
        $config = array();

        $configDir = $this->getConfigDir();
        $distConfigDir = $this->getDistConfigDir();

        foreach ($this->configKeys as $configKey) {
            $fullPath = $configDir . '/' . $configKey . '.yml';
            $fullDistPath = $distConfigDir . '/' . $configKey . '.yml';
            $config[$configKey] = array();

            $userConfig = array();
            if ($this->filesystem->exists($fullPath)) {
                $userConfig = Yaml::parse(file_get_contents($fullPath));
            }

            if ($this->filesystem->exists($fullDistPath)) {
                $distConfig = Yaml::parse(file_get_contents($fullDistPath));
            } else {
                throw new \RuntimeException(sprintf(
                    'Could not find dist config at path (%s)',
                    $fullDistPath
                ));
            }

            $config[$configKey] = new Config(array_merge(
                $distConfig,
                $userConfig
            ));
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

    public function getPhpcrshConfig()
    {
        return $this->getConfig('phpcrsh');
    }

    /**
     * Initialize a configuration files
     */
    public function initConfig(OutputInterface $output = null, $noInteraction = false)
    {
        $log = function ($message) use ($output) {
            if ($output) {
                $output->writeln($message);
            }
        };

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            throw new \RuntimeException('This feature is currently only supported on Linux and OSX (maybe). Please submit a PR to support it on windows.');
        }

        $configDir = $this->getConfigDir();
        $distDir = $this->getDistConfigDir();

        if (!$this->filesystem->exists($configDir)) {
            $log('<info>[+] Creating directory:</info> ' . $configDir);
            $this->filesystem->mkdir($configDir);
        }

        $configFilenames = array(
            'alias.yml',
            'phpcrsh.yml',
        );

        foreach ($configFilenames as $configFilename) {
            $srcFile = $distDir . '/' . $configFilename;
            $destFile = $configDir . '/' . $configFilename;

            if (!$this->filesystem->exists($srcFile)) {
                throw new \Exception('Dist (source) file "' . $srcFile . '" does not exist.');
            }

            if ($this->filesystem->exists($destFile)) {
                if (false === $noInteraction) {
                    $confirmed = $this->questionHelper->askConfirmation(
                        $output,
                        '"' . $configFilename . '" already exists, do you want to overwrite it?'
                    );

                    if (!$confirmed) {
                        continue;
                    }
                } else {
                    $log(sprintf('<info>File</info> %s <info> already exists, not overwriting.', $destFile));
                }
            }

            $this->filesystem->copy($srcFile, $destFile);
            $log('<info>[+] Creating file:</info> ' . $destFile);
        }
    }
}
