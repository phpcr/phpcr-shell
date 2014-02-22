Feature: Remove retention hold
    In order to remove a retention hold
    As a user that is logged into the shell
    I need to be able to remove a retention hold

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List retention holds
        Given there exists a retention hold named "foobar" on "/tests_general_base"
        Given I execute the "retention:hold:remove /tests_general_base foobar" command
        And I save the session
        Then the command should not fail
        And there should not exist a retention hold named "foobar" on "/tests_general_base"
