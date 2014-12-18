Feature: Command aliases
    In order to be more effective when using the shell
    As a user
    I want to be able to use the default command aliases

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Execute an alias with a quoted string
        Given I execute the "ls 'cms/articles/Title with Spaces'" command
        Then the command should not fail


    Scenario Outline: Execute an alias
        Given I execute the "<command>" command
        Then the command should not fail

        Examples:
            | command | 
            | select * from [nt:unstructured] |
            | cd cms |
            | rm cms |
            | mv cms smc |
            | ls |
            | ls cms |
            | ln cms/articles cms/test/foobar |
            | cat cms/articles/article1/title |


    Scenario: List aliases
        Given I execute the "shell:alias:list" command
        Then the command should not fail
        And I should see a table containing the following rows:
            | Alias | Command |
            | cd    | shell:path:change {arg1} |
            | ls    | node:list {arg1} |
