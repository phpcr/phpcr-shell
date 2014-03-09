Feature: Reload the current session
    In order to reload the data from the persistatance layer
    As a user logged into the shell
    I need to be able to run a command which updates the session data

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Refesh the session
        Given I create a node at "/foobar"
        And I execute the "session:refresh" command
        Then the command should not fail
        And there should exist a node at "/foobar"

    Scenario: Refesh the session and keep changes
        Given I create a node at "/foobar"
        And I execute the "session:refresh --keep-changes" command
        Then the command should not fail
        And there should exist a node at "/foobar"

