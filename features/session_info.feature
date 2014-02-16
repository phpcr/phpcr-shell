Feature: Show information about current session
    In order to find out details about the current session
    As a user logged into the shell
    I want to execute the "session:info" command to show a list of available information about the session

    Scenario: Run the session info command
        Given that I am logged in as "testuser"
        And I execute the "session:info" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Key  | Value |
            | user id | admin |
            | workspace name | default |
