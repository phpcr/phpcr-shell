Feature: Import repository data from an XML file
    In order to import data into the repository
    As a user logged into the shell
    I want to be able to import data from an XML file

    Background:
        Given the file "data.xml" contains the contents of "session_data.xml"
        And that I am logged in as "testuser"
        And the current workspace is "default"
        And the current workspace is empty

    Scenario: Import XML non existing file
        Given I execute the "session:import / does-not-exist.xml" command
        Then the command should fail
        And the output should contain:
        """
        The file "does-not-exist.xml" does not exist
        """

    Scenario: Import XML file into the repository base path
        Given I execute the "session:import / data.xml" command
        Then the command should not fail
        And I save the session
        And the following nodes should exist:
            | /tests_general_base |
            | /tests_general_base/multiValueProperty/deepnode |

    Scenario Outline: Specifying UUID behavior
        Given I create a node at "<path>"
        And I execute the "session:import <path> data.xml --uuid-behavior=<uuid_behavior>" command
        Then the command should not fail

        Examples:
            | path | uuid_behavior               |
            | /a | create-new                  |
            | /b | collision-remove-existing   |
            | /c | collision-replace-existing  |
            | /d | collision-throw             |

    Scenario: Specify invalid UUID behavior
        Given I execute the "session:import / data.xml --uuid-behavior=invalid" command
        Then the command should fail
        And I save the session
        And the output should contain:
        """
        The specified uuid behavior "invalid" is invalid, you should use one of:
            - create-new
            - collision-remove-existing
            - collision-replace-existing
            - collision-throw
        """
