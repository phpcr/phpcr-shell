Feature: Save the current session
    In order to persist any changes that have been made in the current session
    As a user logged into the shell
    I need to be able to run a command which persists the changes made in the current session

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Save the session
        Given I execute the "session:save" command
        Then the command should not fail
