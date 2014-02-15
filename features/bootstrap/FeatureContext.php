<?php

require_once(__DIR__.'/../../vendor/autoload.php');

use PHPCR\Shell\Console\Application\SessionApplication;
use Symfony\Component\Console\Input\ArrayInput;
use PHPCR\Shell\Console\Application\ShellApplication;
use PHPCR\Shell\Console\Command\ShellCommand;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    protected $application;
    protected $phpBin;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $phpFinder = new PhpExecutableFinder();
        if (false === $php = $phpFinder->find()) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }

        $this->phpBin = $php;
        $this->phpcrShellBin = realpath(dirname(__FILE__)).'/../../bin/phpcr';
        $this->process = new Process(null);
    }

    private function getOutput()
    {
        $output = $this->process->getErrorOutput() . $this->process->getOutput();

        // Normalize the line endings in the output
        if ("\n" !== PHP_EOL) {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        return trim(preg_replace("/ +$/m", '', $output));
    }

    private function getOutputAsArray()
    {
        return explode("\n", $this->getOutput());
    }

    /**
     * @Given /^that I am logged in as "([^"]*)"$/
     */
    public function thatIAmLoggedInAs($arg1)
    {
    }

    /**
     * @Given /^I execute the "([^"]*)" command$/
     */
    public function iExecuteTheCommand($args)
    {
        $args = strtr($args, array('\'' => '"'));

        $this->process->setCommandLine(
            sprintf(
                '%s %s --transport=%s --command="%s"',
                $this->phpBin,
                $this->phpcrShellBin,
                'jackrabbit',
                $args
            )
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^I should see a table containing the following rows:$/
     */
    public function iShouldSeeATableContainingTheFollowingRows(TableNode $table)
    {
        $output = $this->getOutputAsArray();
        $expectedRows = $table->getRows();
        $foundRows = 0;
        foreach ($expectedRows as $row) {
            foreach ($output as $line) {
                $foundCells = 0;
                foreach ($row as $cell) {
                    if (false !== strpos($line, $cell)) {
                        $foundCells++;
                    }
                }

                if ($foundCells == count($row)) {
                    $foundRows++;
                }
            }
        }

        PHPUnit_Framework_Assert::assertEquals(count($expectedRows), $foundRows, 'Contents: '.$this->getOutput());
    }

}
