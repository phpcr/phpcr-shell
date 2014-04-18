Feature: Clone a node from a given workspace to the current workspace
    In order to clone a node from some workspace to the current workspace
    As a user logged into the shell
    I want to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the current workspace is "default_1"
        And the "session_data.xml" fixtures are loaded
        And the current workspace is "default"
        And the "cms.xml" fixtures are loaded

    Scenario: Clone node no workspace (symlink)
        Given I execute the "node:clone /cms/articles/article1 /cms/clone" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/cms/clone"

    Scenario: Clone node
        Given I execute the "node:clone  /tests_general_base /cms/foobar default_1" command
        Then the command should not fail
        And I save the session
        And there should exist a node at "/cms/foobar"
