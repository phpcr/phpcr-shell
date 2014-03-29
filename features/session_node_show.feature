Feature: Display a detailed view of a single node
    In order to display any node in the current workspace
    As a user logged into the shell
    I want to show all the data available for a node referenced by either its UUID or path

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show node by path
        Given I execute the "session:node:show /tests_general_base" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Property / Node Name          | Type / Node Type | Value                                                   |
            | - jcr:primaryType             | NAME             | nt:unstructured                                         |
