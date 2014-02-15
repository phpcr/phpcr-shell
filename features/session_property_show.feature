Feature: Display a detailed view of a single property
    In order to display the details of a single property
    As a user logged into the shell
    I want to be able to run a command with an absolute path which displays detailed information about a property

    Background:
        Given that I am logged in as "testuser"
        And the "session_data" fixtures are loaded

    Scenario: Show property
        Given: I execute "session:property:show /foobar/barfoo/testproperty"
        Then the command output should contain the following:
        """
        foobar
        """

    Scenario: Show namespaced property
        Given: I execute "session:property:show /foobar/barfoo/[jcr:primaryType]"
        Then the command output should contain the following:
        """
        foobar
        """
