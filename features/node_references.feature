Feature: Show node references
    In order to list which nodes reference the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List weak references
        Given the current node is "/tests_general_base/idExample/jcr:content/weakreference_target""
        And I execute the "node:references --no-ansi" command
        Then the command should not fail
        And I should see table with the following rows::
            | Type | Path |
            | weak | /foobar |
