Feature: Edit a node type
    In order to modify a node type definition
    As a user that is logged into the shell
    I should be able to run a command which launches an external editor to edit the CND as a text file

    Background:
        Given that I am logged in as "testuser"
        And the "example.cnd" node type is loaded

    Scenario Outline: Edit a node type
        Given the "EDITOR" environment variable is set to "cat"
        And I execute the "<command>" command
        Then the command should not fail

        Examples:
            | command | 
            | node-type:edit ns:NodeType |

    Scenario: Edit an existing node type
        Given the "EDITOR" environment variable is set to "cat"
        And I execute the "node-type:edit rep:versionStorage --no-interaction" command
        Then the command should fail
        And I should see the following:
        """
        can't reregister built-in node type
        """

    Scenario: Make an invalid edit
        Given I have an editor which produces the following:
        """"
        asdf asdf asdf 
        """
        And I execute the "node-type:edit ns:NodeType --no-interaction" command
        Then the command should fail
        And I should see the following:
        """
        PARSER ERROR
        """

    Scenario: Create a new node type
        Given I have an editor which produces the following:
        """
        <ns='http://namespace.com/ns'>
        <nt='http://www.jcp.org/jcr/nt/1.0'>
        [ns:somenewtype] > nt:unstructured
        orderable query
        """
        And I execute the "node-type:edit ns:somenewtype --no-interaction" command
        Then the command should not fail
        And there should exist a node type called "ns:somenewtype"

    Scenario: Empty the node type
        Given I have an editor which produces the following:
        """
        """
        And I execute the "node-type:edit ns:NodeType --no-interaction" command
        Then the command should not fail
        And I should see the following:
        """
        Editor emptied the CND file, doing nothing
        """
