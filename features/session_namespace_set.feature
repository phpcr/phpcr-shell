Feature: Set a namespace URI alias
    In order to create or update a namespace alias
    As a user logged into the shell
    I need to be able to run a command which registers an alias with a full URI

    Background:
        Given that I am logged in as "testuser"
        And the "session_data" fixtures are loaded

    Scenario: Register a new namespace alias
        Given I execute "session:namepsace:set foobar http://www.example.com/foobar"
        Then there should exist the namespace alias "foobar" with the URI "http://www.example.com/foobar"

    Scenario: Update namespace alias
        Given there exists the namespace alias "foobar" with the URI "http://www.example.com/foobar"
        And I execute "session:namepsace:set foobar http://www.example.com/barfoo"
        Then there should exist the namespace alias "foobar" with the URI "http://www.example.com/barfoo"

