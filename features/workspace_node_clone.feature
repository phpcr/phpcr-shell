Feature: Clone a node from a given workspace to the current workspace
    In order to clone a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded into a workspace "workspace_two"

    Scenario: Clone node
        Given I execute the "workspace:node:clone workspace_two /tests_general_base/index.txt /index.txt" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/index.txt"

    Scenario: Clone onto existing
        Given there exists a node at "/index.txt"
        And I execute the "workspace:node:clone workspace_two /tests_general_base/index.txt /index.txt" command
        Then the command should fail

    Scenario: Clone onto existing but remove
        Given there exists a node at "/index.txt"
        And I execute the "workspace:node:clone workspace_two /tests_general_base/index.txt /index.txt --remove-existing" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/index.txt"
