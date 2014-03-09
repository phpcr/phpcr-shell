Feature: Remove a retention policy for a given node
    In order to remove the retention policy for a given node
    As a user that is logged into the shell
    I want to be able to excecute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Remove the retention policy on a given node
        Given I execute the "retention:policy:remove /tests_general_base" command
        Then the command should fail
        """
        Unsupported repository operation
        """
