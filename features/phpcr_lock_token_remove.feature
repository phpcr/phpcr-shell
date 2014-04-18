Feature: Remove a lock token in the current session
    In order to create a new lock token
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: Create a new node
        Given I execute the "lock:token:remove foobar" command
        Then the command should fail
        Then I should see the following:
        """
        Not implemented
        """
