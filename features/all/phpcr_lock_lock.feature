Feature: Lock the node at a given path
    In order to lock a node at a given path
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base" has the mixin "mix:lockable"

    Scenario: Create a new lock
        Given I execute the "lock:lock /tests_general_base --deep --session-scoped --timeout=30 --owner-info=random" command
        Then the command should not fail
        And the node "/tests_general_base" should be locked
