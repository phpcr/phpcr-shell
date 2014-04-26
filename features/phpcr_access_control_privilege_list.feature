Feature: List the ac privileges for a given node
    In order to show the privleges for a given node
    As a logged in user
    I want to be able to execute a command which does that

    Scenario: Listing privileges
        Given that I am logged in as "testuser"
        And I execute the "access-control:privilege:list /tests_general_base" command
        Then I should see the following:
        """
        Unsupported repository operation
        """
        #Then the command should not fail
        #And I should see a table containing the following rows:
        #    | Name | Abstract? | Aggregate ? |
        #    | foo | yes | no |

            #    Scenario: Listing all supported privileges
            #        Given I execute the "access-control:privilege:list /tests_general_base --supported --verbose" command
            #        Then the command should not fail
            #        And I should see a table containing the following rows:
            #            | Name | Abstract? | Aggregate ? |
            #            | foo | yes | no |
            #
            #    Scenario: List non node privileges
            #        Given that I am logged in as "testuser"
            #        And I execute the "access-control:privilege:list" command
            #        Then the command should not fail
            #        And I should see a table containing the following rows:
            #            | Name | Abstract? | Aggregate ? |
            #            | foo | yes | no |
