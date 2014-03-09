Feature: Copy a node from a given workspace to the current workspace
    In order to copy a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded into a workspace "test"
        And the "all_property_types.xml" fixtures are loaded into a workspace "default"

    Scenario: Copy node from a different workspace
        Given I execute the "workspace:node:copy /tests_general_base/index.txt /index.txt test" command
        Then the command should not fail
        And there should exist a node at "/index.txt"

    Scenario: Copy node in the same workspace
        And I execute the "workspace:node:copy /tests_general_base/index.txt /index.txt" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/index.txt"
