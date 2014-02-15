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
use Jackalope\RepositoryFactoryJackrabbit;
use PHPCR\SimpleCredentials;
use PHPCR\Util\Console\Helper\PhpcrHelper;
use PHPCR\Util\NodeHelper;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException,
    Behat\Behat\Event\SuiteEvent;
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

        $root = realpath(dirname(__FILE__)).'/../..';
        $this->phpBin = $php;
        $this->phpcrShellBin = $root.'/bin/phpcr';

        $this->process = new Process(null);
    }

    private function getSession()
    {
        $params = array(
            'jackalope.jackrabbit_uri'  => 'http://localhost:8080/server/',
        );
        $factory = new RepositoryFactoryJackrabbit();
        $repository = $factory->getRepository($params);
        $credentials = new SimpleCredentials('admin', 'admin');

        $session = $repository->login($credentials, 'default');

        return $session;
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

    /**
     * @Given /^the "([^"]*)" fixtures are loaded$/
     */
    public function theFixturesAreLoaded($arg1)
    {
        $session = $this->getSession();
        NodeHelper::purgeWorkspace($session);
        $session->save();

        $fixtureFile = realpath(__DIR__).'/../fixtures/'.$arg1.'.xml';
        if (!file_exists($fixtureFile)) {
            throw new \Exception('Fixtures do not exist at ' . $fixturesPath);
        }

        $session->importXml('/', $fixtureFile, 0);
        $session->save();
    }

    /**
     * @Then /^the command should not fail$/
     */
    public function theCommandShouldNotFail()
    {
        $exitCode = $this->process->getExitCode();

        if ($exitCode != 0) {
            die($this->getOutput());
        }

        PHPUnit_Framework_Assert::assertEquals(0, $exitCode, 'Command exited with code ' . $exitCode);
    }

    /**
     * @Then /^the command should fail$/
     */
    public function theCommandShouldFail()
    {
        $exitCode = $this->process->getExitCode();

        PHPUnit_Framework_Assert::assertNotEquals(0, $exitCode, 'Command exited with code ' . $exitCode);
    }

    /**
     * @Given /^the file "([^"]*)" should exist$/
     */
    public function theFileShouldExist($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue(file_exists($arg1));
    }

    /**
     * @Given /^the file "([^"]*)" does not exist$/
     */
    public function theFileDoesNotExist($arg1)
    {
        if (file_exists($arg1)) {
            unlink($arg1);
        }
    }

    /**
     * @Given /^the file "([^"]*)" exists$/
     */
    public function theFileExists($arg1)
    {
        file_put_contents($arg1, '');
    }

    /**
     * @Given /^the output should contain:$/
     */
    public function theOutputShouldContain(PyStringNode $string)
    {
        foreach ($string->getLines() as $line) {
            PHPUnit_Framework_Assert::assertContains($line, $this->getOutput());
        }
    }

    /**
     * @Given /^the node "([^"]*)" should not exist$/
     */
    public function theNodeShouldNotExist($arg1)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        PHPUnit_Framework_Assert::assertNull($node);
    }

    /** @AfterSuite */
    public static function teardown(SuiteEvent $event)
    {
        if (file_exists('foobar.xml')) {
            unlink('foobar.xml');
        }
    }
}
