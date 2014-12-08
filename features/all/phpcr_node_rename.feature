Feature: Rename a node
    In order to rename a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Rename a node
        Given the current node is "/tests_general_base/idExample"
        And I execute the "node:rename . foobar" command
        And I save the session
        Then the command should not fail
        And there should exist a node at "/tests_general_base/foobar"
