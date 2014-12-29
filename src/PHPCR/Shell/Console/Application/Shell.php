<?php

/*
 * This file is part of the PHPCR Shell package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\Shell\Console\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use PHPCR\Shell\Console\Input\StringInput;

/**
 * This is more or less a copy of the Symfony\Component\Shell
 *
 * @author Daniel Leech
 */
class Shell
{
    private $application;
    private $history;
    private $output;
    private $hasReadline;
    private $prompt;

    /**
     * Constructor.
     *
     * If there is no readline support for the current PHP executable
     * a \RuntimeException exception is thrown.
     *
     * @param Application $application An application instance
     */
    public function __construct(Application $application)
    {
        $this->hasReadline = function_exists('readline');
        $this->application = $application;
        $this->history = getenv('HOME').'/.history_'.$application->getName();
        $this->output = new ConsoleOutput();
        $this->prompt = $application->getName().' > ';
    }

    /**
     * Runs the shell.
     */
    public function run()
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);

        if ($this->hasReadline) {
            readline_read_history($this->history);
            readline_completion_function(array($this, 'autocompleter'));
        }

        $this->output->writeln($this->getHeader());

        while (true) {
            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");

                break;
            }

            if ($this->hasReadline) {
                readline_add_history($command);
                readline_write_history($this->history);
            }

            $ret = $this->application->run(new StringInput($command), $this->output);
        }
    }

    /**
     * Returns the shell header.
     *
     * @return string The header string
     */
    protected function getHeader()
    {
        return <<<EOF

Welcome to <info>{$this->application->getName()}</info> (<comment>{$this->application->getVersion()}</comment>).

At the prompt, type <comment>help</comment> for some help.

- To list all of the commands type <comment>list</comment>.
- To list all of the registered command aliases, type <comment>aliases</comment>.

To exit the shell, type <comment>exit</comment>.

For full documentation visit: <info>http://phpcr.readthedocs.org/en/latest/phpcr-shell/index.html</info>

EOF;
    }

    /**
     * Tries to return autocompletion for the current entered text.
     *
     * @param string $text The last segment of the entered text
     *
     * @return Boolean|array A list of guessed strings or true
     */
    private function autocompleter($text)
    {
        // the following does not work at all on my system
        // it only returns the previous line:
        //
        // $info = readline_info();
        // $text = substr($info['line_buffer'], 0, $info['end']);
        $list = $this->application->getContainer()->get('console.input.autocomplete')->autocomplete('');

        return $list;
    }

    /**
     * Reads a single line from standard input.
     *
     * @return string The single line from standard input
     */
    private function readline()
    {
        if ($this->hasReadline) {
            $line = readline($this->prompt);
        } else {
            $this->output->write($this->prompt);
            $line = fgets(STDIN, 1024);
            $line = (!$line && strlen($line) == 0) ? false : rtrim($line);
        }

        return $line;
    }
}
