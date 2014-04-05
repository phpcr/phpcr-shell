Feature: Register a namespace on the workspace
    In order to register a namespace in the current workspace
    As a user logged into the shell
    I should be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"

    # We cannot test namespace registration because jackrabbit does not support
    # namespace unregistration, so we simply try doing something which provokes
    # to throw an exception.
    Scenario: Attemp to register a default namespace
        Given I execute the "workspace:namespace:register foo http\://www.jcp.org/jcr/nt/1.0" command
        Then the command should fail
        And I should see the following:
        """
        Can not change default namespace
        """
