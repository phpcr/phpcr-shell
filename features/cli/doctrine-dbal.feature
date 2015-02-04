Feature: Connect to a doctrine dbal repository
    In order to use the jackalope doctrine-dbal repository
    As a user
    I need to be able to connect to it

    Background:
        Given I initialize doctrine dbal

    Scenario: Connect to doctrine-dbal session
        Given I run PHPCR shell with "--transport=doctrine-dbal --db-driver=pdo_sqlite --db-path=./app.sqlite --command='ls'"
        Then the command should not fail

    Scenario: Connect to doctrine-dbal session create a new profile
        Given I run PHPCR shell with "--transport=doctrine-dbal --db-driver=pdo_sqlite --db-path=./app.sqlite --profile=new --no-interaction --command='ls'"
        Then the command should not fail

    Scenario: Connect to an existing profile
        Given the following profile "phpcrtest" exists:
        """
        transport:
            name: doctrine-dbal
            db_name: phpcrtest
            db_path: app.sqlite
            db_driver: pdo_sqlite
        phpcr:
            workspace: default
            username: admin
            password: admin
        """
        And I run PHPCR shell with "--profile=phpcrtest --no-interaction --command='ls'"
        Then the command should not fail
