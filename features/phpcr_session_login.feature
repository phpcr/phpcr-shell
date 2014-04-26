Feature: Login to the session
    In order to reconnect as a different user from a session
    As a user logged into the shell
    I need to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: Login unauthorized (not existing)
        Given I execute the "session:login foobar barfoo" command
        Then the command should fail
        And I should see the following:
        """
        Unauthorized
        """

    Scenario: Login existing
        Given I execute the "session:login admin admin" command
        Then the command should not fail

    Scenario: Login existing
        Given I execute the "session:login admin admin default_1" command
        Then the command should not fail
        And I execute the "session:info" command
        Then I should see the following:
        """
        default_1
        """
