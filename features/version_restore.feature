Feature: Restore a version
    In order to revert a node to a given version
    As a user logged into the shell
    I need to be able to execute a command which restores a given version

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Restore node version
        Given there exists a node version "asd" for node "/tests_general_base"
        And I execute the "version:restore --version=asd" command
        Then the command should not fail
        And the version of "/tests_general_base" should be "asd"

    Scenario: Restore node version by label
        Given there exists a node version "asd" for node "/tests_general_base"
        And I execute the "version:restore --label=asd" command
        Then the command should not fail
        And the version of "/tests_general_base" should be "asd"

    Scenario: Restore multiple node versions
        Given there exists a node version "asd" for node "/tests_general_base"
        And I execute the "version:restore --version=asd --version=dsa" command
        Then the command should not fail
        And the version of "/tests_general_base" should be "asd"
