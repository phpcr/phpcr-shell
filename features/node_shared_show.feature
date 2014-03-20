Feature: Show the current nodes shared set
    In order to show the shared set to which the current node belongs
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base/daniel/leech" has the mixin "mix:shareable"
        And I clone node "/tests_general_base/daniel/leech" from "default" to "/tests_general_base/bar"

    Scenario: Show the current nodes shared set
        Given the current node is "/tests_general_base/daniel/leech"
        And I execute the "node:shared:show" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
