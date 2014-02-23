Feature: Remove retention hold
    In order to remove a retention hold
    As a user that is logged into the shell
    I need to be able to remove a retention hold

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List retention holds
        Given I execute the "retention:hold:remove /tests_general_base foobar" command
        """
        Unsupported repository operation
        """
