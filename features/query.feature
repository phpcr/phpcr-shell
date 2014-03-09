Feature: Execute a query
    In order to run an SQL query
    As a user logged into the shell
    I want to execute a commad that does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Execute query
        Given I execute the "query 'SELECT a.[jcr:createdBy] FROM [nt:file] AS a'" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | a.jcr:createdBy |
            | admin           |       

    Scenario: Execute query invalid language
        Given I execute the "query 'SELECT * FROM [nt:unstructured]' --language=FRENCH" command
        Then the command should fail
        And I should see the following:
        """
        "FRENCH" is an invalid query language, valid query languages are:
        """
        And I should see the following:
        """
        JCR-SQL2
        """

    Scenario: Execute query with language
        Given I execute the "query '/jcr:root/nodes' --language=xpath" command
        Then the command should not fail
