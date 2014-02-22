Feature: List workspaces
    In order to list the accessible workspaces
    As a user logged into the shell
    I want to execute a commad that does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: List namespaces
        Given I execute the "session:namespace:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Name |
            | default |
