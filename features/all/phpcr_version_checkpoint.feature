Feature: Checkpoint
    In order to commit the version status of a node and checkit out again
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkpoint a a given node
        Given I execute the following commands:
            | cd /tests_version_base/versioned |
            | node:property:set foo bar |
            | session:save |
            | version:checkpoint /tests_version_base/versioned |
            | node:property:set foo baz |
            | session:save |
            | version:checkpoint /tests_version_base/versioned |
        Then the command should not fail
        And I should see the following:
        """
        Version: 1.1
        """
        And I execute the "node:info ." command
        Then I should see the following:
        """
        | Checked out?      | yes
        """

    Scenario: Checkpoint a non versionable node
        Given I execute the "version:checkpoint /tests_version_base" command
        Then the command should fail
        And I should see the following:
        """
        Node "/tests_version_base" is not versionable
        """
