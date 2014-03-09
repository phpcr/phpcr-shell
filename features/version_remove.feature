Feature: Remove node version
    In order to remove a version of a node
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Checkout a a given node
        Given the node "/tests_general_base" has a version with label "mylabel"
        And I execute the "version:remove /tests_general_base mylabel" command
        Then the command should not fail
        And the node "/tests_general_base" should have a node version labeled "mylabel"
