Feature: Show node references
    In order to list which nodes reference the current node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: List weak references
        Given the current node is "/tests_general_base/idExample/jcr:content/weakreference_target"
        And I execute the "node:references . --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Path | Property | Type |
            | /tests_general_base/idExample/jcr:content/weakreference_source1 |ref1 | weak | 
            | /tests_general_base/idExample/jcr:content/weakreference_source2 |ref2 | weak |

    Scenario: List named weak references
        Given the current node is "/tests_general_base/idExample/jcr:content/weakreference_target"
        And I execute the "node:references . ref2 --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Path | Property | Type |
            | /tests_general_base/idExample/jcr:content/weakreference_source2 |ref2 | weak |

    Scenario: List strong references
        Given the current node is "/tests_general_base/idExample"
        And I execute the "node:references . --no-ansi" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Path | Property | Type |
            | /tests_general_base/numberPropertyNode/jcr:content | multiref | strong |
            | /tests_general_base/numberPropertyNode/jcr:content | ref      | strong |

