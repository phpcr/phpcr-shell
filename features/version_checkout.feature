Feature: Checkout a version
    In order to modify a versionable node
    As a user logged into the shell
    I must be able to execute a command which checksout the node

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkout a a given node
        Given I execute the "version:checkout /tests_version_base/versioned" command
        Then the command should not fail
        And the current node is "/tests_version_base/versioned"
        And I execute the "node:info" command
        Then I should see the following:
        """
        | Checked out?      | yes
        """


    Scenario: Checkout a non-versionable node
        Given I execute the "version:checkout /tests_version_base" command
        Then the command should fail
        And I should see the following:
        """
        Node "/tests_version_base" is not versionable
        """
