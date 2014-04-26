Feature: List properites and chidren of current node
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
