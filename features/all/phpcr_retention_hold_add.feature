Feature: Add a retention hold
    In order to add a retention hold on a given node
    As a user that is logged into the shell
    I need to be able to execute a command which adds a retention hold on a node

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Add retention hold
        Given I execute the "retention:hold:add /tests_general_base foobar --deep" command
        Then the command should fail
        And I should see the following:
        """
        Unsupported repository operation
        """
