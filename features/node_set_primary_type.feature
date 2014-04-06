Feature: Set the nodes primary type
    In order to set the primary type of the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List the properties and children of the current node
        Given the current node is "/tests_general_base"
        And I execute the "node:set-primary-type . nt:unstructured --no-ansi" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
