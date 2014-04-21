<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Yaml\Yaml;

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

    private function loadConfig()
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
            return $this->cachedConfig['alias'];
        }

        $this->cachedConfig = $this->loadConfig();

        return $this->cachedConfig['alias'];
    }
}
