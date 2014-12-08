Feature: List the possible lifecycle transitions for the current node
    In order to progress the lifecycle state of a node
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List possible lifecycle transitions
        Given the current node is "/tests_general_base"
        And I execute the "node:lifecycle:list daniel" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """

