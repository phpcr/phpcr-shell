Feature: List all namepsaces mapped to prefixes in the current session
    In order to show which prefixes are mapped to which URIs
    As a user logged into the shell
    I want to run a command which displays a table showing the alias => nameepsace mapping

    Background:
        Given that I am logged in as "testuser"

    Scenario: List namespaces
        Given I execute the "session:namespace:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Prefix     | URI                                       |
            | jcr        | http://www.jcp.org/jcr/1.0                |
            | sv         | http://www.jcp.org/jcr/sv/1.0             |
            | nt         | http://www.jcp.org/jcr/nt/1.0             |
            | mix        | http://www.jcp.org/jcr/mix/1.0            |
            | xml        | http://www.w3.org/XML/1998/namespace      |
            | test       | http://liip.to/jackalope                  |
            | xs         | http://www.w3.org/2001/XMLSchema          |
            | fn_old     | http://www.w3.org/2004/10/xpath-functions |
            | crx        | http://www.day.com/crx/1.0                |
            | lx         | http://flux-cms.org/2.0                   |
            | sling      | http://sling.apache.org/jcr/sling/1.0     |
            | new_prefix | http://a_new_namespace                    |
            | vlt        | http://www.day.com/jcr/vault/1.0          |
            | fn         | http://www.w3.org/2005/xpath-functions    |
            | rep        | internal                                  |
