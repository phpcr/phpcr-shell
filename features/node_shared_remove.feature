Feature: Remove the current node from any shared set to which it belongs
    In order to remove the current node from its corresponding shared set
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base/daniel/leech" has the mixin "mix:shareable"
        And I clone node "/tests_general_base/daniel/leech" from "default" to "/tests_general_base/bar"

    Scenario: Remove the current node and all of its shared paths
        Given the current node is "/tests_general_base/daniel"
        And I execute the "node:shared:remove" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
