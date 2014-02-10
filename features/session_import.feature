Feature: Import repository data from an XML file
    In order to import data into the repository
    As a user logged into the shell
    I want to be able to import data from an XML file

    Background:
        Given the file "import-data.xml" contains
        """
        <?xml version="1.0">
        <some>
        <data>XML</data>
        </some>
        """
        And I am in a shell session

    Scenario: Import XML file into the repository base path
        Given I execute "system:import / import-data.xml
        Then the following nodes should exist:
            | path |
            | /foobar
            | /foobar/foobar
            | /foobar/barfoo

    Scenario Outline: Specifying UUID behavior
        Given I execute the command "system:import / import-data.xml --uuid-behavior=<uui_behavior>
        Then the command should not fail

        Examples:
            | uuid_behavior               |
            | create-new                  |
            | remove-existing             |
            | collision-remove-existing   |
            | collision-replace-existing  |
            | collision-throw             |

    Scenario: Specify invalid UUID behavior
        Given I execute the command "system:import / import-data.xml --uuid-behavior=invalid
        Then the command should fail
        And the output should contain:
        """
        The specified uuid behavior "invalid" is invalid, you should use one of:
            - create-new
            - remove-existing
            - collision-remove-existing
            - collision-replace-existing
            - collision-throw
        """
