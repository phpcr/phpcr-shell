Feature: Show the current nodes shared set
    In order to show the shared set to which the current node belongs
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "cms.xml" fixtures are loaded
        And the current workspace is "default"
        And I clone node "/cms/articles/article1" from "default_1" to "/foobar"

    Scenario: Show the current nodes shared set
        Given the current node is "/foobar"
        And I execute the "node:shared:show ." command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
