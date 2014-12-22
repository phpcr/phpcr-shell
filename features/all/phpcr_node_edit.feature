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
        articles:
            type: Reference
            value: [ 66666fc6-1abf-4708-bfcc-e49511754b40, 77777777-1abf-4708-bfcc-e49511754b40 ]
        article-weak:
            type: WeakReference
            value: 99999999-1abf-4708-bfcc-e49511754b40
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        """
        And I execute the "node:edit cms/products/product1 --no-interaction" command
        Then the command should not fail
        And the property "/cms/products/product1/weight" should have type "Long" and value "10"
        And the property "/cms/products/product1/cost" should have type "Double" and value "12.13"
        And the property "/cms/products/product1/size" should have type "String" and value "XL"
        And the property "/cms/products/product1/name" should have type "String" and value "Product One"
        And the property "/cms/products/product1/articles" should have type "Reference"
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
        And I execute the "node:edit cms/products/productx" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/productx/foobar" should have type "String" and value "FOOOOOOO"

    Scenario: Create a new node with short syntax
        Given I have an editor which produces the following:
        """"
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        foobar: FOOOOOOO
        """
        And I execute the "node:edit cms/products/productx" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/productx/foobar" should have type "String" and value "FOOOOOOO"

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
        And I execute the "node:edit cms/products/productx --type=nt:resource" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And there should exist a node at "/cms/products/productx"
        And the primary type of "/cms/products/productx" should be "nt:resource"

    Scenario: Editor returns empty string
        Given I have an editor which produces the following:
        """"
        """
        And I execute the "node:edit cms/products/productx --no-interaction --type=nt:resource" command
        Then the command should fail

    Scenario: Edit a node by UUID
        Given I have an editor which produces the following:
        """"
        title:
            type: String
            value: 'Article 1'
        'jcr:uuid':
            type: String
            value: 66666fc6-1abf-4708-bfcc-e49511754b40
        tag:
            type: String
            value: [Planes]
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        'jcr:mixinTypes':
            type: Name
            value: ['mix:referenceable']
        tags:
            type: String
            value: [Planes, Trains, Automobiles]
        foobar: FOOOOOOO
        """
        And I execute the "node:edit 66666fc6-1abf-4708-bfcc-e49511754b40 --no-interaction" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/articles/article1/foobar" should have type "String" and value "FOOOOOOO"

    Scenario: Change a property type (cost from Double to Long
        Given I have an editor which produces the following:
        """"
        cost:
            type: Long
            value: 100
        weight:
            type: String
            value: 10
        size:
            value: 10
        'jcr:primaryType':
            type: Name
            value: 'nt:unstructured'
        """
        And I execute the "node:edit cms/products/product1" command
        Then the command should not fail
        And I save the session
        Then the command should not fail
        And the property "/cms/products/product1/cost" should have type "Long" and value "100"
        And the property "/cms/products/product1/weight" should have type "String" and value "10"
        And the property "/cms/products/product1/size" should have type "Long" and value "10"

