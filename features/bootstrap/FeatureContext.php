<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }
    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the test transport is configured$/
     */
    public function theTestTransportIsConfigured()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I am not connected$/
     */
    public function iAmNotConnected()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I execute the "([^"]*)" command$/
     */
    public function iExecuteTheCommand($commandName)
    {
        throw new PendingException();
    }

    /**
     * @Given /^with the "([^"]*)" option set to "([^"]*)"$/
     */
    public function withTheOptionSetTo($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should be connected to the shell$/
     */
    public function iShouldBeConnectedToTheShell()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the session workspace name should be "([^"]*)"$/
     */
    public function theSessionWorkspaceNameShouldBe($arg1)
    {
        throw new PendingException();
    }
}
