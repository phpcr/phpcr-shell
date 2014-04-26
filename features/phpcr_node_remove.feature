Feature: Remove a node
    In order to remove a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Remove a node
        Given the current node is "/tests_general_base"
        And I execute the "node:remove ." command
        Then the command should not fail
        And I save the session
        And there should not exist a node at "/tests_general_base"
