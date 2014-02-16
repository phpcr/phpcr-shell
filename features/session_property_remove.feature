Feature: Remove a single property at a specified path
    In order to remove a single property at a specified path
    As a user logged into the shell
    I want to be able to run a command with an absolute path to a property and have that property removed

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Remove a property
        Given: I execute "session:property:remove /foobar/barfoo/testproperty"
        Then there should not exist a property at "/foobar/barfoo/testproperty"
