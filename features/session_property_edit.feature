Feature: Edit a single property
    In order to edit a single property
    As a user logged into the shell
    I want to be able to open the property in an editor as a temporary file, save it, and return to the shell.

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show property
        Given: I execute "session:property:edit /foobar/barfoo/testproperty"
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
