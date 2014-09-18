Feature: Exit the shell
    In order to quit this damned shell
    As a user
    I want to be able to execute a command which does that.

    Background:
        Given that I am logged in as "testuser"

    Scenario: Make a change and attempt to exist (default to no exit)
        Given I execute the "node:create foo" command
        And I execute the "shell:exit --no-interaction" command
        Then the command should not fail
