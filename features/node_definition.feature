Feature: Show CND for node
    In order to show the compact node definition for a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show node definition
        Given the current node is "/tests_general_base"
        And I execute the "node:definition daniel --no-ansi" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
