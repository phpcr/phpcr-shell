Feature: Update the current node from the node to which it corresponds in the given workspace
    In order to update the current node from the node to which it corresponds in the given workspace
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Rename a node
        Given the current node is "/tests_general_base"
        And I execute the "node:update default" command
        Then the command should not fail
