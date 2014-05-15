Feature: Import an external file as to a node
    In order to import an external file into the system
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Import a file
        Given the file "phpcr.png" contains the contents of "files/phpcrlogos.png"
        Given the current node is "/"
        And I execute the "node:file:import . phpcr.png" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/phpcr.png"
