Feature: List all namepsaces mapped to prefixes in the current session
    In order to show which prefixes are mapped to which URIs
    As a user logged into the shell
    I want to run a command which displays a table showing the alias => nameepsace mapping

    Background:
        Given that I am logged in as "testuser"

    Scenario: List namespaces
        Given I execute the "session:namespace:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Prefix     | URI                                       |
            | jcr        | http://www.jcp.org/jcr/1.0                |
            | rep        | internal                                  |
