Feature: Remove a single property at a specified path
    In order to remove a single property at a specified path
    As a user logged into the shell
    I want to be able to run a command with an absolute path to a property and have that property removed

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Remove a property
        Given I execute the "node:property:remove /cms/articles/article1/title" command
        Then the command should not fail
        And I save the session
        And there should not exist a property at "/cms/articles/article1/title"

    Scenario: Try and remove a node
        And I execute the "node:property:remove /tests_general_base" command
        Then the command should fail
        And I should see the following:
        """
        Could not find a property at "/tests_general_base"
        """
