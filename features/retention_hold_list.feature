Feature: List retention holds
    In order to list the retention holds
    As a user that is logged into the shell
    I need to be able to see the current retention holds

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List retention holds
        Given there exists a retention hold on "/tests_general_base" named "foobar"
        And there exists a "shallow" retention hold on "/tests_general_base" named "barfoo"
        And I execute the "retention:hold:list /tests_general_base" 
        Then the command should not fail
        And I should see a table containing the following rows:
            | Name | Deep? |
            | foobar | yes |
            | barfoo | no | 
