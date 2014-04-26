Feature: Unregister a node type
    In order to unregister a node type
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"

    Scenario: Unregister a node type
        Given the "example.cnd" node type is loaded
        And I execute the "node-type:unregister ns:NodeType" command
        Then the command should fail
        And I should see the following:
        """
        NodeType not found
        """

    Scenario: Attempt to unregister a non-registered node type
        Given the "example.cnd" node type is loaded
        And I execute the "node-type:unregister ns:NodeTypefoobar" command
        Then the command should fail
        And I should see the following:
        """
        NodeType not found
        """
