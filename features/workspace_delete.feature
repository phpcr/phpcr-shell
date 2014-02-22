Feature: Delete a workspace
    In order to delete a new workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: Delete a workspace
        Given there exists a workspace "test"
        And I execute the "workspace:delete test" command
        Then the command should not fail
        And there should not exist a workspace called "test"
