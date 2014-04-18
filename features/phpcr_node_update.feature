Feature: Update the current node from the node to which it corresponds in the given workspace
    In order to update the current node from the node to which it corresponds in the given workspace
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given the current workspace is "default_1"
        And the "cms.xml" fixtures are loaded
        And I set the value of property "title" on node "/cms/articles/article1" to "this is a test"
        And the current workspace is "default"
        And I clone node "/cms/articles/article1" from "default_1" to "/foobar"

    Scenario: Update a node
        Given the current node is "/foobar"
        And I execute the "node:update . default_1" command
        Then the command should not fail
        And I save the session
        And the node at "/foobar" should have the property "title" with value "this is a test"
