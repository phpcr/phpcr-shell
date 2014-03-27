Feature: Create a node
    In order to create a new node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Create a new node
        Given the current node is "/"
        And I execute the "node:create testcreate" command
        And I save the session
        Then the command should not fail
        And there should exist a node at "/testcreate"

    Scenario: Create a new node with primary node type
        Given the current node is "/"
        And I execute the "node:create testfile nt:folder" command
        And I save the session
        Then the command should not fail
        And there should exist a node at "/testfile"
        And the primary type of "/testfile" should be "nt:folder"

    Scenario: Create a new node at a non-root current node no matching child type
        Given the current node is "/tests_general_base/emptyExample"
        And I execute the "node:create testcreate" command
        Then the command should fail
        And I should see the following:
        """
        No matching child node definition found for `testcreate'
        """

    Scenario: Create a new node at a non-root current node
        Given the current node is "/tests_general_base"
        And I execute the "node:create testcreate nt:folder" command
        And I save the session
        Then the command should not fail
        And there should exist a node at "/tests_general_base/testcreate"
