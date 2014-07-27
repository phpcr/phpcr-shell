Feature: Edit a node
    In order to edit a node
    As a user that is logged into the shell
    I should be able to run a command which does that

    Background:
        Given that I am logged in as "testuser"
        And the "cms.xml" fixtures are loaded

    Scenario: Make a nutral edit
        Given I have an editor which produces the following:
        """"
        weight:
            type: Long
            value: 10
        cost:
            type: Double
            value: 12.13
        size:
            type: String
            value: XL
        name:
            type: String
            value: 'Product One'
        tags:
            type: String
            value: [one, two, three]
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        """
        And I execute the "node:edit cms/products/product1" command
        Then the command should not fail
        And the property "/cms/products/product1/weight" should have type "Long" and value "10"
        And the property "/cms/products/product1/cost" should have type "Double" and value "12.13"
        And the property "/cms/products/product1/size" should have type "String" and value "XL"
        And the property "/cms/products/product1/name" should have type "String" and value "Product One"
        And the property "/cms/products/product1/jcr:primaryType" should have type "Name" and value "nt:unstructured"

    Scenario: Remove some properties
        Given I have an editor which produces the following:
        """"
        weight:
            type: Long
            value: 10
        name:
            type: String
            value: 'Product One'
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        """
        And I execute the "node:edit cms/products/product1" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/product1/weight" should have type "Long" and value "10"
        And the property "/cms/products/product1/name" should have type "String" and value "Product One"
        And the property "/cms/products/product1/jcr:primaryType" should have type "Name" and value "nt:unstructured"
        And there should not exist a property at "/cms/products/product1/cost"
        And there should not exist a property at "/cms/products/product1/size"

    Scenario: Edit some properties
        Given I have an editor which produces the following:
        """"
        weight:
            type: Long
            value: 10
        cost:
            type: Double
            value: 100
        size:
            type: String
            value: XXL
        name:
            type: String
            value: 'Product One'
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        """
        And I execute the "node:edit cms/products/product1" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/product1/weight" should have type "Long" and value "10"
        And the property "/cms/products/product1/cost" should have type "Long" and value "100"
        And the property "/cms/products/product1/size" should have type "String" and value "XXL"
        And the property "/cms/products/product1/name" should have type "String" and value "Product One"
        And the property "/cms/products/product1/jcr:primaryType" should have type "Name" and value "nt:unstructured"

    Scenario: Create a new node
        Given I have an editor which produces the following:
        """"
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        foobar:
            type: String
            value: 'FOOOOOOO'
        """
        And I execute the "node:edit cms/products/product2" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/product2/foobar" should have type "String" and value "FOOOOOOO"

    Scenario: Create a new node with short syntax
        Given I have an editor which produces the following:
        """"
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        foobar: FOOOOOOO
        """
        And I execute the "node:edit cms/products/product2" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/product2/foobar" should have type "String" and value "FOOOOOOO"

    Scenario: Create a new node with a specified type
        Given I have an editor which produces the following:
        """"
        'jcr:primaryType':
            type: Name
            value: 'nt:resource'
        'jcr:data':
            type: Binary
            value: foo
        """
        And I execute the "node:edit cms/products/product2 --type=nt:resource" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/cms/products/product2"
        And the primary type of "/cms/products/product2" should be "nt:resource"
