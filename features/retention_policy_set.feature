Feature: Set a retention policy for a given node
    In order to set the retention policy for a given node
    As a user that is logged into the shell
    I want to be able to excecute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Set the retention policy on a given node
        Given I execute the "retention:policy:set /tests_general_baser barfoo" command
        Then the command should not fail
        And the node at "/tests_general_base" should have the retention policy "foobar"
