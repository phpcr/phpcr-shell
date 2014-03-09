Feature: Remove the current node from any shared set to which it belongs
    In order to remove the current node from its corresponding shared set
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded into workspace "workspace_a"
        And the "session_data.xml" fixtures are loaded into workspace "workspace_b"
        And the node at "/tests_general_base" in "workspace_a" is cloned to "/foobar" in "workspace_b"

    Scenario: Rename a node
        Given the current node is "/tests_general_base"
        And I execute the "node:shared:show" command
        Then the command should not fail
