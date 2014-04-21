Feature: Reload the configuration
    In order to reload the configuration
    As a user
    I want to be able to execute a command which does that

    Scenario: Reload configuration
        Given I execute the "shell:config:reload" command
        Then the command should not fail
