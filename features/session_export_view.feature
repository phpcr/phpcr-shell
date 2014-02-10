Feature: Export the repository to an XML file
    In order to export the repository (or part of the repository) to an XML file
    As a user that is logged into the shell
    I want to be able to "session:export:view" command to export the repository to an XML file.

    Background:
        Given I am in a shell session
        And The following nodes exist:
            | path               |
            | /cms/foobar        |
            | /cms/barfoo        |
            | /cms/foobar/barfoo |

    Scenario: Export the root
        Given I execute "session:export:view / foobar.xml"
        Then The file "foobar.xml" should not be empty
        And the command exit code should be "0"

    Scenario: Export a subtree
        Given I execute "session:export:view /cms foobar.xml"
        Then The file "foobar.xml" should not be empty
        And the command exit code should be "0"

    Scenario: Export with an invalid path
        Given I execute "session:export:view cms"
        Then the command should fail
        And the output should contain:
        """
        "cms" is not an absolute path
        """

    Scenario: Export to an existing file
        Given I execute "session:export:view / foobar.xml"
        And the file "foobar.xml" exists
        Then the command should fail
        And the output should contain
        """
        The file "foobar.xml" exists
        """

    Scenario: Export non recursive
        Given I execute "session:export:view / foobar.xml --non-recursive"
        Then the file "foobar.xml" should contain:
        """
        File contents here please
        """

    Scenario: Export and skip binaries
        Given I execute "session:export:view / foobar.xml --skip-binary"
        And the node at "/cms/foobar" contains the binary file "somepicture.jpg"
        Then the file "foobar.xml" should contain:
        """
        What this file should contain
        """

    Scenario: Export the document view
        Given I execute "session:export:view / foobar.xml --document
        Then the file "foobar.xml" should contain:
        """
        What this file should contain
        """
