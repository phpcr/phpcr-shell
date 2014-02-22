<?php

require_once(__DIR__.'/../../vendor/autoload.php');

use Jackalope\RepositoryFactoryJackrabbit;
use PHPCR\SimpleCredentials;
use PHPCR\Util\NodeHelper;
use Symfony\Component\Filesystem\Filesystem;
use PHPCR\PathNotFoundException;
use PHPCR\Shell\Test\ApplicationTester;

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
    protected $applicationTester;
    protected $filesystem;
    protected $workingDir;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpcr-shell' . DIRECTORY_SEPARATOR .
            md5(microtime() * rand(0, 10000));

        $this->workingDir = $dir;

        mkdir($this->workingDir, 0777, true);
        chdir($this->workingDir);
        $this->filesystem = new Filesystem();

        $session = $this->getSession();
        NodeHelper::purgeWorkspace($session);
        $session->save();

        $this->applicationTester = new ApplicationTester($this->application);
        $this->applicationTester->run(array(
            '--transport' => 'jackrabbit',
            '--no-interaction' => true,
        ));
    }

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     */
    public static function cleanTestFolders()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpcr-shell');
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
        return $this->applicationTester->getDisplay();
    }

    private function getOutputAsArray()
    {
        return explode("\n", $this->getOutput());
    }

    private function getXPathForFile($filename)
    {
        $dom = new \DOMDocument(1.0);
        $dom->load($this->getWorkingFilePath($filename));
        $xpath = new \DOMXpath($dom);

        return $xpath;
    }

    private function getFixtureFilename($filename)
    {
        $fixtureFile = realpath(__DIR__).'/../fixtures/'.$filename;
        if (!file_exists($fixtureFile)) {
            throw new \Exception('Fixtures do not exist at ' . $fixtureFile);
        }

        return $fixtureFile;
    }

    private function getWorkingFilePath($filename)
    {
        return $this->workingDir . DIRECTORY_SEPARATOR . $filename;
    }

    private function executeCommand($command)
    {
        $this->applicationTester->runShellCommand($command);
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
        $this->executeCommand($args);
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

        PHPUnit_Framework_Assert::assertEquals(count($expectedRows), $foundRows, $this->getOutput());
    }

    /**
     * @Then /^I should see the following:$/
     */
    public function iShouldSeeTheFollowing(PyStringNode $string)
    {
        $output = $this->getOutput();
        PHPUnit_Framework_Assert::assertContains($string->getRaw(), $output);
    }

    /**
     * @Given /^the "([^"]*)" fixtures are loaded$/
     */
    public function theFixturesAreLoaded($arg1)
    {
        $fixtureFile = $this->getFixtureFilename($arg1);
        $session = $this->getSession();
        $session->importXml('/', $fixtureFile, 0);
        $session->save();
    }

    /**
     * @Then /^the command should not fail$/
     */
    public function theCommandShouldNotFail()
    {
        $exitCode = $this->applicationTester->getLastExitCode();

        if ($exitCode != 0) {
            throw new \Exception('Command failed: (' . $exitCode . ') ' . $this->getOutput());
        }

        PHPUnit_Framework_Assert::assertEquals(0, $exitCode, 'Command exited with code ' . $exitCode);
    }

    /**
     * @Then /^the command should fail$/
     */
    public function theCommandShouldFail()
    {
        $exitCode = $this->applicationTester->getLastExitCode();

        PHPUnit_Framework_Assert::assertNotEquals(0, $exitCode, 'Command exited with code ' . $exitCode);
    }

    /**
     * @Then /^the command should fail with message "([^"]*)"$/
     */
    public function theCommandShouldFailWithMessage($arg1)
    {
        $exitCode = $this->applicationTester->getLastExitCode();
        $output = $this->getOutput();

        PHPUnit_Framework_Assert::assertEquals($arg1, $output);
    }
    /**
     * @Given /^the file "([^"]*)" should exist$/
     */
    public function theFileShouldExist($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue(file_exists($this->getWorkingFilePath($arg1)));
    }

    /**
     * @Given /^the file "([^"]*)" does not exist$/
     */
    public function theFileDoesNotExist($arg1)
    {
        if (file_exists($this->getWorkingFilePath($arg1))) {
            unlink($this->getWorkingFilePath($arg1));
        }
    }

    /**
     * @Given /^the file "([^"]*)" exists$/
     */
    public function theFileExists($arg1)
    {
        file_put_contents($this->getWorkingFilePath($arg1), '');
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

    /**
     * @Given /^the xpath count "([^"]*)" is "([^"]*)" in file "([^"]*)"$/
     */
    public function theXpathCountIsInFile($arg1, $arg2, $arg3)
    {
        $xpath = $this->getXPathForFile($arg3);
        $res = $xpath->query($arg1);

        PHPUnit_Framework_Assert::assertEquals($arg2, $res->length);
    }

    /**
     * @Given /^the file "([^"]*)" contains the contents of "([^"]*)"$/
     */
    public function theFileContainsTheContentsOf($arg1, $arg2)
    {
        $fixtureFile = $this->getFixtureFilename($arg2);
        $this->filesystem->copy($fixtureFile, $this->getWorkingFilePath($arg1));
    }

    /**
     * @Then /^the following nodes should exist:$/
     */
    public function theFollowingNodesShouldExist(TableNode $table)
    {
        $session = $this->getSession();

        foreach ($table->getRows() as $row) {
            try {
                $node = $session->getNode($row[0]);
            } catch (PathNotFoundException $e) {
                throw new PathNotFoundException('Node ' . $row[0] . ' not found');
            }
        }
    }

    /**
     * Depends on session:info command
     *
     * @Then /^I should not be logged into the session$/
     */
    public function iShouldNotBeLoggedIntoTheSession()
    {
        $this->executeCommand('session:info');
        $output = $this->getOutput();
        PHPUnit_Framework_Assert::assertRegExp('/live .*no/', $output);
    }

    /**
     * @Given /^there exists a namespace prefix "([^"]*)" with URI "([^"]*)"$/
     */
    public function thereExistsANamespacePrefixWithUri($arg1, $arg2)
    {
        $session = $this->getSession();
        $session->setNamespacePrefix($arg1, $arg2);
    }

    /**
     * @Then /^there should not exist a namespace prefix "([^"]*)"$/
     */
    public function thereShouldNotExistANamespacePrefix($arg1)
    {
        $session = $this->getSession();
        $session->getNamespacePrefix($arg1);
    }

    /**
     * @Given /^there should exist a node at "([^"]*)"$/
     */
    public function thereShouldExistANodeAt($arg1)
    {
        $session = $this->getSession();
        try {
            $session->getNode($arg1);
        } catch (PathNotFoundException $e) {
            throw new \Exception('Node does at path ' . $arg1 . ' does not exist.');
        }
    }

    /**
     * @Given /^there should not exist a node at "([^"]*)"$/
     */
    public function thereShouldNotExistANodeAt($arg1)
    {
        $session = $this->getSession();

        try {
            $session->getNode($arg1);
            throw new \Exception('Node does at path ' . $arg1 . ' exists.');
        } catch (PathNotFoundException $e) {
            // good.. not does not exist
        }
    }

    /**
     * @Given /^there exists a property at "([^"]*)"$/
     */
    public function thereExistsAPropertyAt($arg1)
    {
        $session = $this->getSession();
        $session->getProperty($arg1);
    }

    /**
     * @Given /^there should not exist a property at "([^"]*)"$/
     */
    public function thereShouldNotExistAPropertyAt($arg1)
    {
        $session = $this->getSession();

        try {
            $session->getProperty($arg1);
            throw new \Exception('Property exists at "' . $arg1 . '"');
        } catch (PathNotFoundException $e) {
            // good
        }
    }

    /**
     * @Given /^the "([^"]*)" environment variable is set to "([^"]*)"$/
     */
    public function theEnvironmentVariableIsSetTo($arg1, $arg2)
    {
        putenv($arg1 . '=' . $arg2);
    }

    /**
     * @Then /^then I should be logged in as "([^"]*)"$/
     */
    public function thenIShouldBeLoggedInAs($arg1)
    {
        $session = $this->getSession();
        $userId = $session->getUserID();

        PHPUnit_Framework_Assert::assertEquals($userId, $arg1);
    }

    /**
     * @Given /^I save the session$/
     */
    public function iSaveTheSession()
    {
        $this->executeCommand('session:save');
    }

    /**
     * @Given /^I create a node at "([^"]*)"$/
     */
    public function iCreateANodeAt($arg1)
    {
        $session = $this->getSession();
        NodeHelper::createPath($session, $arg1);
        $session->save();
    }
}
