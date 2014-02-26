Feature: Edit a node type
    In order to modify a node type definition
    As a user that is logged into the shell
    I should be able to run a command which launches an external editor to edit the CND as a text file

    Background:
        Given that I am logged in as "testuser"
        And the "example.cnd" node type is loaded

    Scenario Outline: Edit a property
        Given the "EDITOR" environment variable is set to "cat"
        And I execute the "<command>" command
        Then the command should not fail

        Examples:
            | command | 
            | node-type:edit ns:NodeType |

    Scenario: Make an invalid CND file
        Given the "EDITOR" environment variable is set to "tac"
        And I execute the "node-type:edit rep:versionStorage --no-interaction" command
        Then the command should fail
        And I should see the following:
        """
        can't reregister built-in node type
        """
