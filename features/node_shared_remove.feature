Feature: Remove the current node from any shared set to which it belongs
    In order to remove the current node from its corresponding shared set
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "cms.xml" fixtures are loaded
        And the current workspace is "default"
        And I clone node "/cms/articles/article1" from "default_1" to "/foobar"

    Scenario: Remove the current node and all of its shared paths
        Given the current node is "/foobar"
        And I execute the "node:shared:remove ." command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
