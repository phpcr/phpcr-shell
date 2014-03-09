Feature: Show the current nodes shared set
    In order to show the shared set to which the current node belongs
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded into workspace "workspace_a"
        And the "session_data.xml" fixtures are loaded into workspace "default"
        And the node at "/tests_general_base" in "workspace_a" is cloned to "/foobar" in "default"

    Scenario: Show the current nodes shared set
        Given the current node is "/tests_general_base"
        And I execute the "node:shared:show" command
        Then the command should not fai
        And I should see the following:
        """
        workspace_a: /tests_general_base
        """
