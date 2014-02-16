Feature: Move a node in the current session
    In order to move a single node in the current workspace
    As a user logged into the shell
    I want to move a node from one path to another

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Move node
        Given I execute the "session:node:move /tests_general_base/index.txt /foobar.txt" command
        Then the command should not fail
        And there should exist a node at "/foobar.txt"
        And there should not exist a node at "/tests_general_base/index.txt"
