Feature: clear the screen
    In order to clear the screen
    As a user
    I want to be able to execute a command which does that.

    Background:
        Given that I am logged in as "testuser"

    Scenario: Execute the shell clear command
        Given I execute the "shell:clear" command
        Then the command should not fail
