Feature: Create a new workspace
    In order to create a new workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"

        # Jackrabbit does not allow dropping workspaces, so we cannot reliably
        # test creating workspaces.
        #
        #    Scenario: Create a workspace
        #        Given I execute the "workspace:create footest" command
        #        Then the command should not fail
        #        And there should exist a workspace called "test"
