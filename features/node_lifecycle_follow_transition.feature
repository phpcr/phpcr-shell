Feature: Follow the given lifecycle transition on the current node
    In order to progress the lifecycle state of a node
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Follow lifecycle transition
        Given the current node is "/tests_general_base"
        And I execute the "node:lifecycle:follow daniel foo" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
