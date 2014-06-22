Feature: Show the current nodes shared set
    In order to show the shared set to which the current node belongs
    As a user that is logged into the shell
    I need to be able to do that

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Show the current node
        Given the current node is "/foobar"
        And I execute the "node:show ." command
        Then the command should not fail
        And I should see the following:
        """
        <?xml version="1.0" encoding="UTF-8"?>
        <sv:node xmlns:jcr="http://www.jcp.org/jcr/1.0" xmlns:sv="http://www.jcp.org/jcr/sv/1.0" xmlns:nt="http://www.jcp.org/jcr/nt/1.0" xmlns:mix="http://www.jcp.org/jcr/mix/1.0" xmlns:ns="http://namespace.com/ns" xmlns:test="http://liip.to/jackalope" xmlns:phpcr="http://www.doctrine-project.org/projects/phpcr_odm" xmlns:dcms="http://dcms.com/ns/1.0" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:slinp="asd" xmlns:fn_old="http://www.w3.org/2004/10/xpath-functions" xmlns:crx="http://www.day.com/crx/1.0" xmlns:lx="http://flux-cms.org/2.0" xmlns:sling="http://sling.apache.org/jcr/sling/1.0" xmlns:dtl="http://www.dantleech.com" xmlns:vlt="http://www.day.com/jcr/vault/1.0" xmlns:my_prefix="http://a_new_namespace" xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns:rep="internal" sv:name="jcr:root">
          <sv:property sv:name="jcr:primaryType" sv:type="Name">
            <sv:value>rep:root</sv:value>
          </sv:property>
          <sv:property sv:name="jcr:mixinTypes" sv:type="Name" sv:multiple="true">
            <sv:value>rep:AccessControllable</sv:value>
          </sv:property>
        </sv:node>
        """
