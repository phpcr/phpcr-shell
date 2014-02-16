Feature: Display a detailed view of a single node
    In order to display any node in the current workspace
    As a user logged into the shell
    I want to show all the data available for a node referenced by either its UUID or path

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show node by path
        Given: I execute "session:node:show /foobar/barfoo"
        Then the command output should contain the following:
        """
        Foobar
        """
