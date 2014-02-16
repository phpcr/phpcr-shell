Feature: List all namepsaces mapped to prefixes in the current session
    In order to show which prefixes are mapped to which URIs
    As a user logged into the shell
    I want to run a command which displays a table showing the alias => nameepsace mapping

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Move node
        Given: I execute "session:namepsace:list"
        Then the command output should contain the following:
        """
        Foobar
        """

