Feature: List Repository Descriptors
    In order to show the repositories descriptors
    As a logged in user
    I want to be able to execute a command which lists the repository descriptors

    Scenario: Listing the descriptors
        Given that I am logged in as "testuser"
        And I execute the "repository:descriptor:list" command
        Then I should see a table containing the following rows:
            | Key                   | Value                       |
            | jcr.repository.name   | Jackrabbit                  |
            | jcr.repository.vendor | Apache Software Foundation  |
