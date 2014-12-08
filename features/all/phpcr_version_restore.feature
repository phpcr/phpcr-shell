Feature: Restore a version
    In order to revert a node to a given version
    As a user logged into the shell
    I need to be able to execute a command which restores a given version

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Restore node version
        Given I execute the following commands:
            | cd /tests_version_base/versioned |
            | node:property:set foo initalbar |
            | session:save |
            | version:checkpoint /tests_version_base/versioned |
            | node:property:set foo baz |
            | session:save |
            | version:checkpoint /tests_version_base/versioned |
        And I execute the "version:restore /tests_version_base/versioned 1.0" command
        Then the command should not fail
        And I execute the "ls" command
        Then I should see the following:
        """
        | foo                | STRING    | inital
        """
