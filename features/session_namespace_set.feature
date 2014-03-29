Feature: Set a namespace URI alias
    In order to create or update a namespace alias
    As a user logged into the shell
    I need to be able to run a command which registers an alias with a full URI

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Register a new namespace alias
        Given I execute the "session:namespace:set foobar http://www.example.com/foobar" command
        Then the command should fail
        And I should see the following:
        """
        TODO: implement session scope remapping of namespaces
        """
