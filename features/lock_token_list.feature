Feature: List the lock tokens registered with the current session
    In order to list the lock tokens registered in the current session
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: List lock tokens
        Given I execute the "lock:token:add foobar" command
        Then the command should fail
        """
        Not implemented
        """
