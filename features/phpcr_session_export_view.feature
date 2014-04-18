Feature: Export the repository to an XML file
    In order to export the repository (or part of the repository) to an XML file
    As a user that is logged into the shell
    I want to be able to "session:export:view" command to export the repository to an XML file.

    Background:
        Given that I am logged in as "testuser"
        And the file "foobar.xml" does not exist
        And the "session_data.xml" fixtures are loaded

    Scenario: Export the root
        Given I execute the "session:export:view / foobar.xml" command
        Then the command should not fail
        And the file "foobar.xml" should exist
        And the xpath count "/sv:node" is "1" in file "foobar.xml"
        And the xpath count "/sv:node/sv:node" is "1" in file "foobar.xml"

    Scenario: Export a subtree
        Given I execute the "session:export:view /tests_general_base foobar.xml" command
        Then the file "foobar.xml" should exist
        And the command should not fail

    Scenario: Export with an invalid path
        Given I execute the "session:export:view cms foobar.xml" command
        Then the command should fail
        And the output should contain:
        """
        Invalid path 'cms'
        """

    Scenario: Export to an existing file
        Given the file "foobar.xml" exists
        And I execute the "session:export:view /tests_general_base foobar.xml" command
        Then the command should fail
        And the output should contain:
        """
        File "foobar.xml" already exists.
        """

    Scenario: Export non recursive
        Given I execute the "session:export:view /tests_general_base foobar.xml --no-recurse" command
        Then the command should not fail
        And the file "foobar.xml" should exist
        And the xpath count "/sv:node" is "1" in file "foobar.xml"
        And the xpath count "/sv:node/sv:node" is "0" in file "foobar.xml"

    Scenario: Export and skip binaries
        Given I execute the "session:export:view / foobar.xml --skip-binary" command
        Then the command should not fail

    Scenario: Export the document view
        Given I execute the "session:export:view / foobar.xml --document" command
        Then the command should not fail
