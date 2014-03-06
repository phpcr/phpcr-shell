Feature: Show CND for node
    In order to show the compact node definition for a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "session_data.xml" fixtures are loaded

    Scenario: Rename a node
        Given the current node is "/tests_general_base"
        And I execute the "node:definition --no-ansi" command
        Then the command should not fail
        And I should see the following:
        """
        <nt='http://www.jcp.org/jcr/nt/1.0'>
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
