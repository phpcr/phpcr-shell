Feature: Show the details of a lock
    In order to show the details of a lock
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded
        And the node at "/tests_general_base" has the mixin "mix:lockable"
        And the node "/tests_general_base" is locked

    Scenario: Create a new node
        Given I execute the "lock:info /tests_general_base" command
        Then the command should fail
        And I should see the following:
        """
        Not implemented
        """
        # And I should see the following:
        # """
        # Lock Owner: admin
        # Lock Token: foobar
        # Seconds Remaining: 123
        # Deep?: yes
        # Live?: yes
        # Owned by current session?: no
        # Session Scoped?: no
        # """

