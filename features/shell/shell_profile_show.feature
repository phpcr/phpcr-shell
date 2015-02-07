Feature: Show the profile
    In order to inspect the current profile
    As a user
    I need to be able to execute a command that does that

    Scenario: Dump config
        Given I execute the "shell:profile:show --no-ansi --no-interaction" command
        Then the command should not fail
        And I should see the following:
        """
        | name        | jackrabbit                          |
        """

