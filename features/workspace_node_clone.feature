Feature: Clone a node from a given workspace to the current workspace
    In order to clone a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "cms.xml" fixtures are loaded
        And the current workspace is "default"
        And the "cms.xml" fixtures are loaded

    Scenario: Clone node
        Given the current workspace is "default"
        And I execute the "workspace:node:clone default_1 /cms/articles/article1 /cms/clone" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/cms/clone"

    Scenario: Clone onto existing
        Given I execute the "workspace:node:clone default_1 /cms/articles/article1 /cms/articles" command
        Then the command should fail
        And I should see the following:
        """
        Node already exists at destination
        """

    Scenario: Clone onto existing but remove
        Given I execute the "workspace:node:clone --remove-existing default_1 /cms/articles/article1 /cms/articles/article1" command
        Then the command should not fail
