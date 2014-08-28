Feature: Execute a a raw UPDATE query in JCR_SQL2
    In order to run an UPDATE JCR_SQL2 query easily
    As a user logged into the shell
    I want to simply type the query like in a normal sql shell

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario Outline: Execute update query
        Given I execute the "<query>" command
        Then the command should not fail
        And I save the session
        And the node at "<path>" should have the property "<property>" with value "<expectedValue>"
        And I should see the following:
        """
        1 row(s) affected
        """
        Examples:
            | query | path | property | expectedValue |
            | UPDATE [nt:unstructured] AS a SET a.title = 'DTL' WHERE localname() = 'article1' | /cms/articles/article1 | title | DTL |
            | update [nt:unstructured] as a set a.title = 'dtl' where localname() = 'article1' | /cms/articles/article1 | title | dtl |
            | UPDATE nt:unstructured AS a SET a.title = 'DTL' WHERE localname() = 'article1' | /cms/articles/article1 | title | DTL |
            | UPDATE nt:unstructured AS a SET title = 'DTL' WHERE localname() = 'article1' | /cms/articles/article1 | title | DTL |
            | UPDATE nt:unstructured AS a SET title = 'DTL', foobar='barfoo' WHERE localname() = 'article1' | /cms/articles/article1 | foobar | barfoo |

    Scenario: Replace a multivalue index by value
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags = 'Rockets' WHERE a.tags = 'Trains'" command
        And I save the session
        Then the command should not fail
        And the node at "/cms/articles/article1" should have the property "tags" with value "Rockets" at index "1"

    Scenario: Set a multivalue value
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags = ['Rockets', 'Dragons'] WHERE a.tags = 'Trains'" command
        And I save the session
        Then the command should not fail
        And the node at "/cms/articles/article1" should have the property "tags" with value "Rockets" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Dragons" at index "1"

    Scenario: Update single multivalue
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags = 'Rockets' WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Rockets" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Automobiles" at index "2"

    Scenario: Update single multivalue without selector
        Given I execute the "UPDATE [nt:unstructured] SET tags = 'Rockets' WHERE tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Rockets" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Automobiles" at index "2"

    Scenario: Remove single multivalue
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags = NULL WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Trains" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Automobiles" at index "1"

    Scenario: Remove single multivalue by index
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags[0] = NULL WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Trains" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Automobiles" at index "1"

    Scenario: Add a multivalue property
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags[] = 'Kite' WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Planes" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Automobiles" at index "2"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Kite" at index "3"

    Scenario: Replace a multivalue property by index
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags[1] = 'Kite', a.tags[2] = 'foobar' WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tags" with value "Planes" at index "0"
        And the node at "/cms/articles/article1" should have the property "tags" with value "Kite" at index "1"
        And the node at "/cms/articles/article1" should have the property "tags" with value "foobar" at index "2"

    Scenario: Replace a multivalue property by invalid index
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags[10] = 'Kite' WHERE a.tags = 'Planes'" command
        Then the command should fail
        And I should see the following:
        """
        Multivalue index "10" does not exist 
        """

    Scenario: Replace a multivalue property by invalid index with array (invalid)
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags[1] = ['Kite'] WHERE a.tags = 'Planes'" command
        Then the command should fail
        And I should see the following:
        """
        Cannot set index to array value on "tags"
        """
