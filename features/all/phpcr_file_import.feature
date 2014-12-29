Feature: Import an external file as to a node
    In order to import an external file into the system
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded
        And the file "phpcr.png" contains the contents of "files/phpcrlogos.png"
        And the current node is "/"

    Scenario: Import a file
        Given I execute the "file:import . phpcr.png" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/phpcr.png"
        And the node at "/phpcr.png/jcr:content" should have the property "jcr:mimeType" with value "image/png"

    Scenario: Import a file onto existing file, no overwrite specified
        Given I execute the "file:import . phpcr.png" command
        And I execute the "file:import . phpcr.png" command
        Then the command should fail

    Scenario: Import a file onto existing file, force not specified
        Given I execute the "file:import . phpcr.png --no-interaction" command
        And I execute the "file:import . phpcr.png" command
        Then the command should fail

    Scenario: Import non-regular file onto existing file, force not specified
        Given I execute the "file:import foo ." command
        Then the command should fail

    Scenario: Import a file onto existing file, force specified
        Given I execute the "file:import . phpcr.png" command
        And I execute the "file:import . phpcr.png --force" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/phpcr.png"
        And the node at "/phpcr.png/jcr:content" should have the property "jcr:mimeType" with value "image/png"

    Scenario: Import a file, override mime type
        Given I execute the "file:import . phpcr.png --mime-type=application/data" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/phpcr.png"
        And the node at "/phpcr.png/jcr:content" should have the property "jcr:mimeType" with value "application/data"

    Scenario: Import a file, specify a name
        Given I execute the "file:import ./foobar.png phpcr.png --mime-type=application/data" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/foobar.png"

    Scenario: Import a file to a specified property
        Given I execute the "file:import ./ phpcr.png --no-container" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a property at "/phpcr.png"

    Scenario: Import overwrite a specified property
        Given I execute the "file:import ./ phpcr.png --no-container" command
        And I save the session
        And I execute the "file:import ./ phpcr.png --no-container" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a property at "/phpcr.png"
