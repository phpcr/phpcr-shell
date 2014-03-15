Feature: Register a namespace on the workspace
    In order to register a namespace in the current workspace
    As a user logged into the shell
    I should be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "clean.xml" system view fixtures are loaded

    Scenario: List namespaces
        Given I execute the "workspace:namespace:register dcms http://foobar.com/ns" command
        Then the command should not fail
