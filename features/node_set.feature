Feature: Set a node property
    In order to set a property on a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "all_property_types.xml" fixtures are loaded
        And the current node path is "/properties"

    Scenario Outline: Set a property
        Given I execute the "<command>" command
        Then the command should not fail
        And the node at "/properties" should have the property "<name>" with type "<type>"

        Examples:
            | command | name | type |
            | node:set uri http://foobar | uri | http://foobar | url | 
            | node:set double 12.12 | double | 12.12 | double |
            | node:set long 123 | long | 123 | long |
            | node:set multivalue value1 --index=0 | multivalue | value1 | string |
            | node:set multivalue value2 --index=1 | multivalue | value2 | string |
            | node:set thisisnew foobar string | /properties/thisisnew | foobar | string |
