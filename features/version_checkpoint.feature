Feature: Checkpoint
    In order to commit the version status of a node and checkit out again
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkpoint a a given node
        Given I execute the "version:checkpoint /tests_version_base/versioned" command
        Then the command should not fail
        And the node "/tests_version_base/verionable" should be checked out
        And I should see the following:
        """
        Version:
        """

    Scenario: Checkpoint a non versionable node
        Given I execute the "version:checkpoint /tests_version_base" command
        Then the command should fail
        And I should see the following:
        """
        Node "/tests_version_base" is not versionable
        """
