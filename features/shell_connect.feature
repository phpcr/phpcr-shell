Feature: Launch a new shell session
    In order to administer a PHPCR repository
    As a user
    I need to be able to launch the PHPCRSH

    Scenario: Connect
        Given I execute the "shell:config:reload" command
        Then the command should not fail

