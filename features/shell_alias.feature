Feature: Default aliases
    In order to be more effective when using the shell
    As a user
    I want to be able to use the default command aliases

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario Outline: Execute an alias
        Given I execute the "<command>" command
        Then the command should not fail

        Examples:
            | command | 
            | use default |
            | select * from [nt:unstructured] |
            | cd cms |
            | rm cms |
            | mv cms smc |
            | ls |
            | ls cms |
