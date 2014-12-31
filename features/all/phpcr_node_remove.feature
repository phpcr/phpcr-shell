Feature: Remove a node
    In order to remove a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Remove the current node
        Given the current node is "/cms/test"
        And I execute the "node:remove ." command
        Then the command should not fail
        And I save the session
        And there should not exist a node at "/cms/test"
        And the current node should be "/cms"

    Scenario: Remove a non-current node
        Given the current node is "/cms"
        And I execute the "node:remove /cms/users/daniel" command
        Then the command should not fail
        And I save the session
        And there should not exist a node at "/cms/users/daniel"
        And the current node should be "/cms"

    Scenario: Delete root node
        Given the current node is "/"
        And I execute the "node:remove ." command
        Then the command should fail
        And I should see the following:
        """
        You cannot delete the root node
        """

    Scenario: Delete root node by wildcard
        Given I execute the "node:remove /tests_general_base/*" command
        Then the command should not fail
        And I save the session
        And there should not exist a node at "/tests_general_base/daniel"

    Scenario: Delete node by UUID
        Given the current node is "/"
        And I execute the "node:remove 88888888-1abf-4708-bfcc-e49511754b40" command
        Then the command should not fail

    Scenario: Delete referenced node
        Given I execute the "node:remove /cms/articles/article1" command
        Then the command should fail
        And I should see the following:
        """
        The node "/cms/articles/article1" is referenced by the following properties
        """

    Scenario: Delete weak referenced node
        Given I execute the "node:remove /cms/articles/article3" command
        Then the command should not fail

    Scenario: Remove the current node and all of its shared paths
        Given the current node is "/cms/articles"
        And I execute the "node:remove . --shared" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
