Feature: Execute a raw query in JCR_SQL2
    In order to run an JCR_SQL2 query easily
    As a user logged into the shell
    I want to simply type the query like in a normal sql shell

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Execute query
        Given I execute the "SELECT a.[jcr:createdBy], a.[jcr:primaryType] from [nt:folder] AS a WHERE localname() = 'emptyExample'" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | a.jcr:createdBy | a.jcr:primaryType |
            | admin                   | nt:folder                 |
