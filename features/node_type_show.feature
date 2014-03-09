Feature: Show a node type
    In order to show the CND definition of a node type
    As a user that is logged into the shell
    I need to be able to execute a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Execute the note-type show command
        Given I execute the "node-type:show nt:unstructured" command
        Then the command should not fail
        And I should see the following:
        """
[nt:unstructured] > nt:base
      orderable query
      - *
      multiple jcr.operator.equal.to', 'jcr.operator.not.equal.to', 'jcr.operator.greater.than', 'jcr.operator.greater.than.or.equal.to', 'jcr.operator.less.than', 'jcr.operator.less.than.or.equal.to', 'jcr.operator.like
      - *
      jcr.operator.equal.to', 'jcr.operator.not.equal.to', 'jcr.operator.greater.than', 'jcr.operator.greater.than.or.equal.to', 'jcr.operator.less.than', 'jcr.operator.less.than.or.equal.to', 'jcr.operator.like
      + * (nt:base)
      = nt:unstructured
      VERSION sns
        """

    Scenario: Execute the note-type show command
        Given I execute the "node-type:show nt:not-exist" command
        Then the command should fail
        And I should see the following:
        """
        The node type "nt:not-exist" does not exist
        """
