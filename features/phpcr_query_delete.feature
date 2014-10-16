Feature: Execute a a raw DELETE query in JCR_SQL2
    In order to run an DELETE JCR_SQL2 query easily
    As a user logged into the shell
    I want to simply type the query like in a normal sql shell

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario Outline: Execute query
        Given I execute the "<query>" command
        Then the command should not fail
        And there should exist a node at "<path>"
        And I save the session
        And there should not exist a node at "<path>"
        And I should see the following:
        """
        1 row(s) affected
        """
        Examples:
            | query | path |
            | DELETE FROM [nt:unstructured] AS a WHERE localname() = 'product1' | /cms/products/product1 |
            | delete FROM [nt:unstructured] as a where localname() = 'product1' | /cms/products/product1 |
            | DELETE FROM nt:unstructured AS a WHERE localname() = 'product1' | /cms/products/product1 |
