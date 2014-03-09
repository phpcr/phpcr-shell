Feature: Logout of the session
    In order to disconnect from the session
    As a user logged into the shell
    I need to be able to disconnect from the session

    Background:
        Given that I am logged in as "testuser"

    Scenario: Logout
        Given I execute the "session:login foobar barfoo" command
        Then the command should not fail
        And I should be logged into the session
