Feature: Unlock the node at a given path
    In order to unlock a node at a given path
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base" has the mixin "mix:lockable"

    Scenario: Create a new node
        Given I execute the "lock:lock /tests_general_base --session-scoped" command
        And I execute the "lock:unlock /tests_general_base" command
        Then the command should not fail
        And the node "/tests_general_base" should not be locked
