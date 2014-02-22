Feature: Show a retention policy for a given node
    In order to display the retention policy for a given node
    As a user that is logged into the shell
    I want to be able to excecute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Get retention policy on a given node
        Given there exists a retention policy named "foobar" on "/tests_general_base"
        Given I execute the "retention:policy:get /tests_general_base" command
        Then the command should not fail
        And I should see the following:
        """
        foobar
        """
