Feature: List retention holds
    In order to list the retention holds
    As a user that is logged into the shell
    I need to be able to see the current retention holds

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List retention holds
        Given I execute the "retention:hold:list /tests_general_base" command
        Then the command should fail
        And I should see the following:
        """
        Unsupported repository operation
        """
