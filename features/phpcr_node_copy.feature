Feature: Copy a node from a given workspace to the current workspace
    In order to copy a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "session_data.xml" fixtures are loaded
        And the current workspace is "default"
        And the "session_data.xml" fixtures are loaded

    Scenario: Copy node in the same workspace
        Given I execute the "node:copy /tests_general_base/index.txt /foo" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/foo"

    Scenario: Copy node from a different workspace
        Given I execute the "node:copy /tests_general_base/index.txt /index.txt default_1" command
        Then the command should not fail
        And there should exist a node at "/index.txt"
