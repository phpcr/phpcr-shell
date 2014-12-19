Feature: Set a node property
    In order to set a property on a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "all_property_types.xml" fixtures are loaded
        Given the current node is "/properties"

    Scenario Outline: Set a property
        Given I execute the "<command>" command
        Then the command should not fail
        And I save the session
        Then the node at "/properties" should have the property "<name>" with value "<value>"
        Examples:
            | command | name | type | value |
            | node:property:set uri http://foobar | uri | uri | http://foobar |
            | node:property:set double 12.12 --type=double | double | double | 12.12 |
            | node:property:set long 123 | long | long | 123 |
            | node:property:set thisisnew foobar --type=string | thisisnew | string | foobar |
            | node:property:set fooref /properties/reference_target --type=reference | fooref | string | 13543fc6-1abf-4708-bfcc-e49511754b40 |
            | node:property:set fooref /properties/reference_target --type=weakreference | fooref | string | 13543fc6-1abf-4708-bfcc-e49511754b40 |
            | node:property:set fooref 13543fc6-1abf-4708-bfcc-e49511754b40 --type=weakreference | fooref | string | 13543fc6-1abf-4708-bfcc-e49511754b40 |

    Scenario: Update a property but do not specify the type
        Given I execute the "node:property:set /properties/decimal 1234" command
        And I execute the "node:list /properties" command
        Then I should see the following:
        """
        decimal          | DECIMAL 
        """

    Scenario: Create a new property
        Given I execute the "node:property:set /properties/new 1234" command
        Then the command should not fail
        And I execute the "node:list /properties" command
        Then I should see the following:
        """
        new              | STRING
        """
