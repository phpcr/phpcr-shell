Feature: Switch to given workspace
    In order to change the current workspace
    As a user logged into the shell
    I want to execute a commad that does that

    Background:
        Given that I am logged in as "testuser"
        And there exists a workspace "foobar"

    Scenario: List workspaces
        Given I execute the "workspace:use foobar" command
        Then the command should not fail
        And I execute the "session:info" command
        Then I should see the following:
        """
        foobar
        """
