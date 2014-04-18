Feature: Node Version History
    In order to see the version history of a given node
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "versionable.xml" fixtures are loaded

    Scenario: Checkout a a given node
        Given I execute the "version:history /tests_version_base/versioned" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Name            | Created             |

    Scenario: History on a non versionable node
        Given I execute the "version:history /tests_version_base" command
        Then the command should fail
        And I should see the following:
        """
        Node "/tests_version_base" is not versionable
        """
