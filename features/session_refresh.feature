Feature: Reload the current session
    In order to reload the data from the persistatance layer
    As a user logged into the shell
    I need to be able to run a command which updates the session data

    Background:
        Given that I am logged in as "testuser"
        And the "session_data" fixtures are loaded

    Scenario: Refesh the session
        Given I execute "session:refresh"
        Then the command should not fail

    Scenario: Refesh the session and keep changes
        Given I execute "session:refresh --keep-changes"
        Then the command should not fail
