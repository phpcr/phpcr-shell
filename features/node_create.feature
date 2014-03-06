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
        Then the command should not fail
        And there should exist a node at "/testcreate"

    Scenario: Create a new node at a non-root current node
        Given the current node is "/tests_general_base"
        And I execute the "node:create testcreate" command
        Then the command should not fail
        And there should exist a node at "/tests_general_base/testcreate"

    Scenario: Create a new node at an absolute path
        Given I execute the "node:create /testcreate" command
        Then the command should not fail
        And there should exist a node at "/testcreate"

    Scenario: Create a new node before another node
        Given the current node is "/tests_general_base"
        Given I execute the "node:create foobar --before unversionable" command
        Then the command should not fail
        And there should exist a node at "/tests_general_base/foobar" before "/tests_general_base/unversionable"

    Scenario: Attempt to create an empty node
        Given I execute the "node:create /" command
        Then the command should fail
        And I should see the following
        """
        Invalid node name "/"
        """
