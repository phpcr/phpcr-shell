<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

class ConfigHelper extends Helper
{
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
}
