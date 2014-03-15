Feature: Unregister a namespace on the workspace
    In order to unregister a namespace in the current workspace
    As a user logged into the shell
    I should be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List namespaces
        Given the namespace "http://foobar.com/ns" with prefix "foo" exists
        And I execute the "workspace:namespace:unregister http://foobar.com/ns" command
        Then the command should fail
        And I should see the following:
        """
        not supported by jackrabbit backend
        """
