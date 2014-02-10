Feature: List Repository Descriptors
    In order to show the repositories descriptors
    As a logged in user
    I want to be able to execute a command which lists the repository descriptors

    Scenario: Listing the descriptors
        Given I am in a shell session
        And the repository has the following descriptors:
            | descriptor_key         | value  |
            | SPEC_VERSION_DESC      | 2.0    |
            | REP_VENDOR_SPEC        | DTL    |
            | WRITE_SUPPORTED        | true   |
        And I execute the "repository:descriptor:list" command
        Then I should see a table with 3 rows
