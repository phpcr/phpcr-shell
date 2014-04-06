Feature: Remove node version
    In order to remove a version of a node
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkout a a given node
        Given I execute the following commands:
            | cd /tests_version_base/versioned |
            | version:checkout /tests_version_base/versioned |
            | node:set foo baz |
            | session:save |
            | version:checkin /tests_version_base/versioned |
            | version:checkout /tests_version_base/versioned |
            | node:set foo bar |
            | session:save |
            | version:checkin /tests_version_base/versioned |
        And I execute the "version:remove /tests_version_base/versioned 1.0" command
        Then the command should not fail
        And I execute the "version:history /tests_version_base/versioned" command
        Then I should not see the following:
        """
        | 1.0             |
        """
