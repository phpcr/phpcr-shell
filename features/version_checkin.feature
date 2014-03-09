Feature: Checkin a version
    In order to checkin a version of a given absolute path
    As a user logged into the shell
    I need to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkin a version of a given node
        Given I execute the "version:checkin /tests_version_base/versioned" command
        Then the command should not fail
        And I should see the following:
        """
        Version:
        """

    Scenario: Checkin a non-versionable node
        Given I execute the "version:checkin /tests_version_base" command
        Then the command should fail
        And I should see the following:
        """
        Node "/tests_version_base" is not versionable
        """
