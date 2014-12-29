<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Test;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use PHPCR\Shell\Console\Input\StringInput;
use PHPCR\Shell\Console\Application\ShellApplication;

/**
 * Eases the testing of console applications.
 *
 * When testing an application, don't forget to disable the auto exit flag:
 *
 *     $application = new Application();
 *     $application->setAutoExit(false);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApplicationTester
{
    private $application;
    private $shellApplication;
    private $input;
    private $output;
    private $lastExitCode;

    /**
     * Constructor.
     *
     * @param Application $application An Application instance to test.
     */
    public function __construct(Application $application, ShellApplication $shellApplication = null)
    {
        $this->output = new StreamOutput(fopen('php://memory', 'w', false));

        $this->application = $application;
        $this->shellApplication = $shellApplication;
    }

    /**
     * Executes the application.
     *
     * Available options:
     *
     *  * interactive: Sets the input interactive flag
     *  * decorated:   Sets the output decorated flag
     *  * verbosity:   Sets the output verbosity flag
     *
     * @param array $input   An array of arguments and options
     * @param array $options An array of options
     *
     * @return integer The command exit code
     */
    public function run(array $input, $options = array())
    {
        $this->input = new ArrayInput($input);

        if (isset($options['decorated'])) {
            $this->output->setDecorated($options['decorated']);
        }
        if (isset($options['verbosity'])) {
            $this->output->setVerbosity($options['verbosity']);
        }

        $this->application->setAutoExit(false);

        if ($this->shellApplication) {
            $this->shellApplication->setAutoExit(false);
        }
        $this->application->setCatchExceptions(false);

        return $this->application->run($this->input, $this->output);
    }

    /**
     * Gets the display returned by the last execution of the application.
     *
     * @param Boolean $normalize Whether to normalize end of lines to \n or not
     *
     * @return string The display
     */
    public function getDisplay($normalize = false)
    {
        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    public function getLastLine()
    {
        $display = trim($this->getDisplay());
        $lines = explode("\n", $display);

        if ($lines) {
            return end($lines);
        }

        return $display;
    }

    /**
     * Gets the input instance used by the last execution of the application.
     *
     * @return InputInterface The current input instance
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the output instance used by the last execution of the application.
     *
     * @return OutputInterface The current output instance
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function runShellCommand($command)
    {
        if ($this->shellApplication) {
            $ret = $this->shellApplication->run(new StringInput($command), $this->output);
        } else {
            $ret = $this->application->run(new StringInput($command), $this->output);
        }

        $this->lastExitCode = $ret;

        return $ret;
    }

    public function getLastExitCode()
    {
        return $this->lastExitCode;
    }
}
