Feature: List properites and chidren of current nodeA
    In order to list the properties and children of the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List the properties and children of the current node
        Given the current node is "/tests_general_base"
        And I execute the "node:list --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | idExample/                    | nt:file   |                           |
            | test:namespacedNode           | nt:folder |                           |
            | emptyExample                  | nt:folder |                           |
            | multiValueProperty/           | nt:folder |                           |
            | numberPropertyNode/           | nt:file   |                           |
            | NumberPropertyNodeToCompare1/ | nt:file   |                           |
            | NumberPropertyNodeToCompare2/ | nt:file   |                           |
            | jcr:primaryType               | NAME      | nt:unstructured           |

    Scenario: List the properties
        Given the current node is "/tests_general_base"
        And I execute the "node:list --properties --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | jcr:primaryType              | NAME      | nt:unstructured           |

    Scenario: List the children nodes
        Given the current node is "/tests_general_base"
        And I execute the "node:list --children --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | index.txt/                    | nt:file   |                           |
            | idExample/                    | nt:file   |                           |
            | test:namespacedNode           | nt:folder |                           |
            | emptyExample                  | nt:folder |                           |
            | multiValueProperty/           | nt:folder |                           |
            | numberPropertyNode/           | nt:file   |                           |
            | NumberPropertyNodeToCompare1/ | nt:file   |                           |
            | NumberPropertyNodeToCompare2/ | nt:file   |                           |


    Scenario: List node hierarchy
        Given the current node is "/"
        And I execute the "node:list --level=1" command
        Then the command should not fail
        And I should see the following:
        """
        daniel
        """

    Scenario: Show templates
        Given the current node is "/tests_general_base"
        And I execute the "node:list --template" command
        Then the command should not fail
        And I should see the following:
        """
        | @*                            | nt:base         |                 |
        """

    Scenario: List node by UUID
        Given I execute the "node:list 842e61c0-09ab-42a9-87c0-308ccc90e6f4" command
        Then the command should not fail
        And I should see the following:
        """
        jcr:uuid
        """

    Scenario: Catch exception on invalid reference
        Given I execute the "node:list /tests_general_base/numberPropertyNode/jcr:content" command
        Then the command should not fail
        And I should see the following:
        """
        One or more weak reference targets have not been found
        """

    Scenario: Wildcard on name
        Given I execute the "node:list /tests_general_base/numberPropertyNode/jcr:con*" command
        Then the command should not fail
        And I should see the following:
        """
        +-------------+-----------------+--+
        | jcr:content | nt:unstructured |  |
        +-------------+-----------------+--+
        """

    Scenario: Wildcard on directory
        Given I execute the "node:list /tests_general_base/*/jcr:content" command
        Then the command should not fail
        And I should see the following:
        """
/tests_general_base/index.txt [nt:file] > nt:hierarchyNode
+-------------+-----------------+--+
| jcr:content | nt:unstructured |  |
+-------------+-----------------+--+
/tests_general_base/idExample [nt:file] > nt:hierarchyNode
+--------------+-----------------+--+
| jcr:content/ | nt:unstructured |  |
+--------------+-----------------+--+
/tests_general_base/numberPropertyNode [nt:file] > nt:hierarchyNode
+-------------+-----------------+--+
| jcr:content | nt:unstructured |  |
+-------------+-----------------+--+
/tests_general_base/NumberPropertyNodeToCompare1 [nt:file] > nt:hierarchyNode
+-------------+-----------------+--+
| jcr:content | nt:unstructured |  |
+-------------+-----------------+--+
/tests_general_base/NumberPropertyNodeToCompare2 [nt:file] > nt:hierarchyNode
+-------------+-----------------+--+
| jcr:content | nt:unstructured |  |
+-------------+-----------------+--+
"""

    Scenario: Wildcard from relative path
        Given the current node is "/tests_general_base"
        And I execute the "node:list numberPropertyNode/jcr:con*" command
        Then the command should not fail
        And I should see the following:
        """
        +-------------+-----------------+--+
        | jcr:content | nt:unstructured |  |
        +-------------+-----------------+--+
        """

    Scenario: Wildcard from relative path 2
        Given the current node is "/tests_general_base"
        And I execute the "node:list num*" command
        Then the command should not fail
        And I should see the following:
        """
        +---------------------+---------+--------------+
        | numberPropertyNode/ | nt:file | +jcr:content |
        +---------------------+---------+--------------+
        """
