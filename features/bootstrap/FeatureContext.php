<?php

require_once(__DIR__.'/../../vendor/autoload.php');

use Jackalope\RepositoryFactoryJackrabbit;
use PHPCR\SimpleCredentials;
use PHPCR\Util\NodeHelper;
use Symfony\Component\Filesystem\Filesystem;
use PHPCR\PathNotFoundException;
use PHPCR\Shell\Test\ApplicationTester;
use PHPCR\Util\PathHelper;

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
    protected $currentWorkspaceName = 'default';

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

        $session = $this->getSession(null, true);

        $this->applicationTester = new ApplicationTester($this->application);
        $this->applicationTester->run(array(
            '--transport' => 'jackrabbit',
            '--no-interaction' => true,
            '--unsupported' => true, // test all the commands, even if they are unsupported (we test for the fail)
        ), array(
            'interactive' => true,
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

    private function getSession($workspaceName = null, $force = false)
    {
        if ($workspaceName === null) {
            $workspaceName = $this->currentWorkspaceName;
        }

        static $sessions = array();

        if (false === $force && isset($sessions[$workspaceName])) {
            $session = $sessions[$workspaceName];
            return $session;
        }

        $params = array(
            'jackalope.jackrabbit_uri'  => 'http://localhost:8080/server/',
        );
        $factory = new RepositoryFactoryJackrabbit();

        $repository = $factory->getRepository($params);
        $credentials = new SimpleCredentials('admin', 'admin');

        $sessions[$workspaceName] = $repository->login($credentials, $workspaceName);

        return $sessions[$workspaceName];
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
     * @Given /^I execute the following commands:$/
     */
    public function iExecuteTheFollowingCommands(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            $this->executeCommand($row[0]);
            $this->theCommandShouldNotFail();
        }
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
                    if (!$cell) {
                        $foundCells++;
                        continue;
                    }

                    if (false !== strpos($line, $cell)) {
                        $foundCells++;
                    }
                }

                if ($foundCells == count($row)) {
                    $foundRows++;
                }
            }
        }

        PHPUnit_Framework_Assert::assertGreaterThanOrEqual(count($expectedRows), $foundRows, $this->getOutput());
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
     * @Then /^I should not see the following:$/
     */
    public function iShouldNotSeeTheFollowing(PyStringNode $string)
    {
        $output = $this->getOutput();
        PHPUnit_Framework_Assert::assertNotContains($string->getRaw(), $output);
    }

    /**
     * @Given /^the "([^"]*)" fixtures are loaded$/
     */
    public function theFixturesAreLoaded($arg1)
    {
        $fixtureFile = $this->getFixtureFilename($arg1);
        $session = $this->getSession(null, true);
        NodeHelper::purgeWorkspace($session);
        $session->save();
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
     * @Given /^there should exist a node at "([^"]*)" before "([^"]*)"$/
     */
    public function thereShouldExistANodeAtBefore($arg1, $arg2)
    {
        $session = $this->getSession();

        try {
            $node = $session->getNode($arg2);
        } catch (PathNotFoundException $e) {
            throw new \Exception('Node does at path ' . $arg1 . ' does not exist.');
        }

        $parent = $session->getNode(PathHelper::getParentPath($arg2));
        $parentChildren = array_values((array) $parent->getNodes());
        $targetNode = null;

        foreach ($parentChildren as $i => $parentChild) {
            if ($parentChild->getPath() == $arg1) {
                $targetNode = $parentChild;
                $afterNode = $parentChildren[$i + 1];
                break;
            }
        }

        if (null === $targetNode) {
            throw new \Exception('Could not find child node ' . $arg1);
        }

        PHPUnit_Framework_Assert::assertEquals($arg2, $afterNode->getPath());
    }

    /**
     * @Given /^the node at "([^"]*)" should have the mixin "([^"]*)"$/
     */
    public function theNodeAtShouldHaveTheMixin($arg1, $arg2)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        $mixinNodeTypes = $node->getMixinNodeTypes();

        foreach ($mixinNodeTypes as $mixinNodeType) {
            if ($mixinNodeType->getName() == $arg2) {
                return;
            }
        }

        throw new \Exception('Node "' . $arg1 . '" does not have node type "' . $arg2 . '"');
    }

    /**
     * @Given /^the node at "([^"]*)" should not have the mixin "([^"]*)"$/
     */
    public function theNodeAtShouldNotHaveTheMixin($arg1, $arg2)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        $mixinNodeTypes = $node->getMixinNodeTypes();

        foreach ($mixinNodeTypes as $mixinNodeType) {
            if ($mixinNodeType->getName() == $arg2) {
                throw new \Exception('Node "' . $arg1 . '" has the node type "' . $arg2 . '"');
            }
        }
    }

    /**
     * @Given /^there should not exist a node at "([^"]*)"$/
     */
    public function thereShouldNotExistANodeAt($arg1)
    {
        $session = $this->getSession(null, true);

        try {
            $session->getNode($arg1);
            throw new \Exception('Node at path ' . $arg1 . ' exists.');
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
    /**
     * @Given /^there should exist a workspace called "([^"]*)"$/
     */
    public function thereShouldExistAWorkspaceCalled($arg1)
    {
        $session = $this->getSession();
        $accessibleWorkspaceNames = $session->getWorkspace()->getAccessibleWorkspaceNames();
        if (!in_array($arg1, $accessibleWorkspaceNames)) {
            throw new \Exception(sprintf('Workspace "%s" is not accessible', $arg1));
        }
    }

    /**
     * @Given /^there does not exist a workspace called "([^"]*)"$/
     */
    public function thereDoesNotExistAWorkspaceCalled($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $workspace->deleteWorkspace($arg1);
    }

    /**
     * @Given /^there exists a workspace "([^"]*)"$/
     */
    public function thereExistsAWorkspace($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        try {
            $workspace->createWorkspace($arg1);
        } catch (\Exception $e) {
            // already exists..
        }
    }

    /**
     * @Given /^there should not exist a workspace called "([^"]*)"$/
     */
    public function thereShouldNotExistAWorkspaceCalled($arg1)
    {
        try {
            $this->thereShouldExistAWorkspaceCalled($arg1);
            throw new \Exception(sprintf('Workspace "%s" exists.', $arg1));
        } catch (\Exception $e) {
        }
    }

    /**
     * @Given /^the "([^"]*)" fixtures are loaded into workspace "([^"]*)"$/
     */
    public function theFixturesAreLoadedIntoWorkspace($arg1, $arg2)
    {
        $this->theCurrentWorkspaceIs($arg2);
        $fixtureFile = $this->getFixtureFilename($arg1);
        $session = $this->getSession();
        NodeHelper::purgeWorkspace($session);
        $session->save();
        $session->importXml('/', $fixtureFile, 0);
        $session->save();
    }

    /**
     * @Given /^the "([^"]*)" node type is loaded$/
     */
    public function theNodeTypeIsLoaded($arg1)
    {
        $fixtureFile = $this->getFixtureFilename($arg1);
        $cnd = file_get_contents($fixtureFile);
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $nodeTypeManager = $workspace->getNodeTypeManager();
        $nodeTypeManager->registerNodeTypesCnd($cnd, true);
    }

    /**
     * @Given /^there should exist a node type called "([^"]*)"$/
     */
    public function thereShouldExistANodeTypeCalled($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $nodeTypeManager = $workspace->getNodeTypeManager();
        $nodeTypeManager->getNodeType($arg1);
    }

    /**
     * @Given /^there should not exist a node type named "([^"]*)"$/
     */
    public function thereShouldNotExistANodeTypeNamed($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $nodeTypeManager = $workspace->getNodeTypeManager();
        try {
            $nodeTypeManager->getNodeType($arg1);
        } catch (\Exception $e) {
            var_dump(get_class($e));die();
        }

        throw new \Exception('Node type ' . $arg1 . ' exists');
    }

    /**
     * @Given /^I have an editor which produces the following:$/
     */
    public function iHaveAnEditorWhichProducesTheFollowing(PyStringNode $string)
    {
        $tmpFile = $this->workingDir . DIRECTORY_SEPARATOR . 'fake-editor-file';
        $editorFile = $this->workingDir . DIRECTORY_SEPARATOR . 'fakeed';
        file_put_contents($tmpFile, $string->getRaw());
        chmod($tmpFile, 0777);
        $script = array();
        $script[] = '#!/bin/bash';
        $script[] = 'FILE=$1';
        $script[] = 'cat ' . $tmpFile . ' > $FILE';

        file_put_contents($editorFile, implode("\n", $script));
        chmod($editorFile, 0777);
        putenv('EDITOR=' . $editorFile);
    }

    /**
     * @Given /^the current node is "([^"]*)"$/
     */
    public function theCurrentNodeIs($arg1)
    {
        $this->executeCommand(sprintf('cd %s', $arg1));
    }

    /**
     * @Given /^the current workspace is "([^"]*)"$/
     */
    public function theCurrentWorkspaceIs($arg1)
    {
        $this->thereExistsAWorkspace($arg1);
        $this->currentWorkspaceName = $arg1;
    }

    /**
     * @Given /^the current workspace is empty/
     */
    public function theCurrentWorkspaceIsEmpty()
    {
        $session = $this->getSession();
        NodeHelper::purgeWorkspace($session);
        $session->save();
    }

    /**
     * @Given /^I refresh the session/
     */
    public function iRefreshTheSession()
    {
        $this->executeCommand('session:refresh');
    }

    /**
     * @Given /^the primary type of "([^"]*)" should be "([^"]*)"$/
     */
    public function thePrimaryTypeOfShouldBe($arg1, $arg2)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        $primaryTypeName = $node->getPrimaryNodeType()->getName();
        PHPUnit_Framework_Assert::assertEquals($arg2, $primaryTypeName, 'Node type of ' . $arg1 . ' is ' . $arg2);
    }

    /**
     * @Given /^the node at "([^"]*)" should have the property "([^"]*)" with value "([^"]*)"$/
     */
    public function theNodeAtShouldHaveThePropertyWithValue($arg1, $arg2, $arg3)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        $property = $node->getProperty($arg2);
        $propertyType = $property->getValue();
        PHPUnit_Framework_Assert::assertEquals($arg3, $propertyType);
    }

    /**
     * @Given /^I set the value of property "([^"]*)" on node "([^"]*)" to "([^"]*)"$/
     */
    public function iSetTheValueOfPropertyOnNodeTo($arg1, $arg2, $arg3)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg2);
        $property = $node->getProperty($arg1);
        $property->setValue($arg3);
        $session->save();
    }

    /**
     * @Given /^the node at "([^"]*)" has the mixin "([^"]*)"$/
     */
    public function theNodeAtHasTheMixin($arg1, $arg2)
    {
        $session = $this->getSession();
        $node = $session->getNode($arg1);
        $node->addMixin($arg2);
        $session->save();
    }

    /**
     * @Given /^I clone node "([^"]*)" from "([^"]*)" to "([^"]*)"$/
     */
    public function iCloneNodeFromTo($arg1, $arg2, $arg3)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $workspace->cloneFrom($arg2, $arg1, $arg3, true);
    }

    /**
     * @Given /^the node "([^"]*)" is locked$/
     */
    public function theNodeIsLocked($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $lockManager->lock($arg1, true, true);
    }

    /**
     * @Given /^the node "([^"]*)" is not locked$/
     */
    public function theNodeIsNotLocked($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        if ($lockManager->isLocked($arg1)) {
            $lockManager->unlock($arg1);
        }
    }

    /**
     * @Given /^the node "([^"]*)" should be locked$/
     */
    public function theNodeShouldBeLocked($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $isLocked = $lockManager->isLocked($arg1);

        PHPUnit_Framework_Assert::assertTrue($isLocked);
    }

    /**
     * @Given /^the node "([^"]*)" should not be locked$/
     */
    public function theNodeShouldNotBeLocked($arg1)
    {
        $session = $this->getSession();
        $workspace = $session->getWorkspace();
        $lockManager = $workspace->getLockManager();
        $isLocked = $lockManager->isLocked($arg1);

        PHPUnit_Framework_Assert::assertFalse($isLocked);
    }
}
