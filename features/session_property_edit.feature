Feature: Edit a single property
    In order to edit a single property
    As a user logged into the shell
    I want to be able to open the property in an editor as a temporary file, save it, and return to the shell.

    Background:
        Given that I am logged in as "testuser"
        And the "EDITOR" environment variable is set to "cat"
        And the "all_property_types.xml" fixtures are loaded

    Scenario Outline: Edit a property
        Given I execute the "<command>" command
        Then the command should not fail

        Examples:
            | command | 
            | session:property:edit /properties/uri |
            | session:property:edit /properties/double |
            | session:property:edit /properties/binary |
            | session:property:edit /properties/long |
            | session:property:edit /properties/reference |
            | session:property:edit /properties/date |
            | session:property:edit /properties/path |
            | session:property:edit /properties/string |
            | session:property:edit /properties/weakreference |
            | session:property:edit /properties/decimal |
            | session:property:edit /properties/multivalue 0|
            | session:property:edit /properties/multivalue 1|

    Scenario: Edit multivalue property, no index
        Given I execute the "session:property:edit /properties/multivalue" command
        Then the command should fail
        And I should see the following:
        """
        You specified a multivalue property but did not provide an index
        """

    Scenario: Edit multivalue property, out of range
        Given I execute the "session:property:edit /properties/multivalue 100" command
        Then the command should fail
        And I should see the following:
        """
        The multivalue index you specified ("100") does not exist
        """
