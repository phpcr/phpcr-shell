Feature: List registered node types
    In order to list all of the registered node types
    As a user that is logged into the shell
    I need to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"

    Scenario: List node types
        Given I execute the "node-type:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Name                       | Primary Item Name | Abstract? | Mixin? | Queryable? |
            | nt:folder                  | no                | no        | yes    | yes        |

    Scenario: List node types with filter
        Given I execute the "node-type:list mix.*" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Name                       | Primary Item Name | Abstract? | Mixin? | Queryable? |
            | mix:created           |                   | no        | yes    | yes        |
