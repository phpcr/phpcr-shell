Feature: Refresh the TTL of a lock
    In order to reset the TTL on the lock of a given node path
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And I execute the "lock:lock /tests_general_base --session-scoped --timeout=10" command

    Scenario: Create a new node
        And I execute the "lock:refresh /tests_general_base" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
