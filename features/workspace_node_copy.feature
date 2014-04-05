Feature: Copy a node from a given workspace to the current workspace
    In order to copy a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "session_data.xml" fixtures are loaded
        And the current workspace is "default"
        And I purge the current workspace

    Scenario: Copy node from a different workspace
        Given I execute the "workspace:node:copy /tests_general_base/index.txt /index.txt default_1" command
        Then the command should not fail
        And there should exist a node at "/index.txt"

    Scenario: Copy node in the same workspace
        And I execute the "workspace:node:copy /tests_general_base/index.txt /tests_general_base/index.txt.2" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/tests_general_base/index.txt.2"
