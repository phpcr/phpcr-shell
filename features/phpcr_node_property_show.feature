Feature: Display the contents of a single property
    In order to display the contents of a single property
    As a user logged into the shell
    I want to be able to run a command with an absolute path which displays the contents of a property

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Show binary property
        Given I execute the "node:property:show /tests_general_base/index.txt/jcr:content/jcr:data" command
        Then the command should not fail
        And I should see the following:
        """
h1. Chapter 1 Title
      
* foo
* bar
** foo2
** foo3
* foo0

|| header || bar ||
| h | j |

{code}
hello world
{code}
"""

    Scenario: Show date property
        Given I execute the "node:property:show /tests_general_base/index.txt/jcr:content/mydateprop" command
        Then the command should not fail
        And I should see the following:
        """
2011-04-21T14:34:20+01:00
"""

    Scenario: Try to show non-existing property
        Given I execute the "node:property:show /this/path/does/not/exist" command
        Then the command should fail
        And I should see the following:
        """
        There is no property at the path "/this/path/does/not/exist"
        """
