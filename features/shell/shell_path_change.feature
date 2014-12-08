Feature: Change the shells current working path
    In order to change the current working path of the shell
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Change the current working path to root
        Given the current node is "/tests_general_base"
        And I execute the "shell:path:change /" command
        Then the command should not fail
        And the current node should be "/"

    Scenario: Change the current working path
        Given the current node is "/"
        And I execute the "shell:path:change /tests_general_base" command
        Then the command should not fail
        And the current node should be "/tests_general_base"

    Scenario: Change the current working path with a UUID
        Given the current node is "/"
        And I execute the "shell:path:change 842e61c0-09ab-42a9-87c0-308ccc90e6f4" command
        Then the command should not fail
        And the current node should be "/tests_general_base/idExample"
