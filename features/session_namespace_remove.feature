Feature: Remove a namespace URI alias
    In order to remove a namespace alias
    As a user logged into the shell
    I need to be able to run a command which removes an namespace alias from the session

    Background:
        Given that I am logged in as "testuser"
        And the "session_data" fixtures are loaded

    Scenario: Remove a namespace alias
        Given there exists the namespace alias "foobar" with the URI "http://www.example.com/foobar"
        And I execute "session:namepsace:remove foobar"
        Then there should not exist the namespace alias "foobar"

