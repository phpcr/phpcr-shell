Feature: List the capabilities of the current repository
    In order to show the capabilities of the current repository
    As a logged in user
    I want to be able to execute a command which lists the repository descriptors

    Scenario: Listing the capabilities
        Given that I am logged in as "testuser"
        And I execute the "repository:capability:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Key                   | Value                       |
