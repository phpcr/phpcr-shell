Feature: Load a CND file
    In order to load a node type definition from a CND file
    As a user that is logged into the shell
    I need to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the file "NodeType.cnd" contains the contents of "example.cnd"

    Scenario: Attempt to load a non-existing file
        Given I execute the "node-type:load notexists.cnd" command
        Then the command should fail
        And I should see the following:
        """
        The CND file "notexists.cnd" does not exist
        """

    Scenario: Load the given node type from a file and allow updating
        Given I execute the "node-type:load NodeType.cnd --update" command
        Then the command should not fail
        And there should exist a node type called "ns:NodeType"
