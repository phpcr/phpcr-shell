Feature: Show information about node
    In order to show some useful information about the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Rename a node
        Given the current node is "/tests_general_base"
        And I execute the "node:info --no-ansi" command
        Then the command should not fail
        And I should see the following:
        """
        Identifier: /tests_general_base
        Path: /tests_general_base
        Index: 0
        Primary node type: nt:unstructured
        Mixin node types: [ mixin:foobar, mixin:barfoo ]
        Checked out: No
        """
