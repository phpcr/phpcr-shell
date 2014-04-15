Feature: Move a node in the current session
    In order to move a single node in the current workspace
    As a user logged into the shell
    I want to move a node from one path to another

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Move node
        Given I execute the "node:move /tests_general_base/index.txt /foobar" command
        Then the command should not fail
        And I execute the "session:save" command
        And there should exist a node at "/foobar"
        And there should not exist a node at "/tests_general_base/index.txt"

    Scenario: Move node relative paths
        Given the current node is "/tests_general_base/index.txt"
        And I execute the "node:move . /barfoo" command
        Then the command should not fail
        And I execute the "session:save" command
        And there should exist a node at "/barfoo"
        And there should not exist a node at "/tests_general_base/index.txt"
