Feature: Show information about node
    In order to show some useful information about the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show node information
        Given the current node is "/tests_general_base"
        And I execute the "node:info --no-ansi" command
        Then the command should not fail
        And I should see the following:
        """
        +-------------------+--------------------------------------+
        | Path              | /tests_general_base                  |
        | UUID              | N/A                                  |
        | Index             | 1                                    |
        | Primary node type | nt:unstructured                      |
        | Mixin node types  |                                      |
        | Checked out?      | N/A                                  |
        | Locked?           | [ERROR] Not implemented by jackalope |
        +-------------------+--------------------------------------+
        """
