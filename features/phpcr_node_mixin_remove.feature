Feature: Remove mixin to the current node
    In order to add a mixin to the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base" has the mixin "mix:versionable"

    Scenario: Remove a mixin to the current node
        Given the current node is "/tests_general_base"
        And I execute the "node:mixin:remove mix:versionable --no-ansi" command
        And I save the session
        Then the command should not fail
        And the node at "/tests_general_base" should not have the mixin "mix:versionable"
