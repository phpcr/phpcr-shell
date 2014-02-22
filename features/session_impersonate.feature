Feature: Impersonate another user
    In order to be able to interact with the repository as if I were logged in as another user
    As a user logged into the shell
    I need to be able to impersonate a different user

    Scenario: Impersonate user
        Given that I am logged in as "testuser"
        And I execute the "session:impersonate impersonateuser" command
        Then the command should fail
        And I should see the following:
        """
        Not supported
        """
