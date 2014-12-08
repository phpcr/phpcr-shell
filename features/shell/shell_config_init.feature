Feature: Initialize a new local configuration
    In order to create a default configuration
    As a user
    I want to be able to execute a command which does that

    Scenario: Initialize configuration
        Given I execute the "shell:config:init --no-ansi --no-interaction" command
        Then the command should not fail
        And I should see the following:
        """
        alias.yml
        """
