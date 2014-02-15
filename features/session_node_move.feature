Feature: Move a node in the current session
    In order to move a single node in the current workspace
    As a user logged into the shell
    I want to move a node from one path to another

    Background:
        Given that I am logged in as "testuser"
        And the "session_data" fixtures are loaded

    Scenario: Move node
        Given: I execute "session:node:move /test/node1 /test/node2"
        Then there should exist a node at "/test/node2"
        And there should not exist a node at "/test/node1"
