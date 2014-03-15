Feature: List workspace namespaces and their prefixes
    In order to list the workspaces namespaces and corresponding prefixes
    As a user logged into the shell
    I should be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List namespaces
        Given I execute the "workspace:namespace:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Prefix     | URI                                       |
            | jcr        | http://www.jcp.org/jcr/1.0                |
            | sv         | http://www.jcp.org/jcr/sv/1.0             |
            | nt         | http://www.jcp.org/jcr/nt/1.0             |
