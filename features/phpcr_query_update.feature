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

    Scenario: Update multivalue index by value
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tags = 'Rockets' WHERE a.tags = 'Trains'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        Cannot update property "tags". Updating multi-value nodes with more than one element not currently supported
        """

    Scenario: Update single multivalue
        Given I execute the "UPDATE [nt:unstructured] AS a SET a.tag = 'Rockets' WHERE a.tags = 'Planes'" command
        And I save the session
        Then the command should not fail
        And I should see the following:
        """
        1 row(s) affected
        """
        And the node at "/cms/articles/article1" should have the property "tag" with value "Rockets" at index "0"
