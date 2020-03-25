@placeholder @domain
Feature: In order to extend Pimcore's placeholder
  CoreShop adds a expression placeholder

  Scenario: Test a simple arithmetic expression
    Then the placeholder value for expression "%Expression(expression, {'expression' : '1+1'});" should be "2"

  Scenario: Test a container parameter expression
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'parameter(\'kernel.environment\')'});" should be "test"

  Scenario: Test a coreshop expression language provider for object
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'object(1)'});" should be "/"

  Scenario: Test a coreshop expression language provider for asset
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'asset(1)'});" should be "/"

  Scenario: Test a coreshop expression language provider for document
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'document(1)'});" should be "/"

  Scenario: Test a Pimcore object with expression language placeholder
    Given the site operates on a store in "Austria"
    And the site has a product "Shoe" priced at 100
    Then the placeholder value for expression "%Expression(object, {'expression': 'value.getName()'});" for object should be "Shoe"

  Scenario: Test PHP Function 'sprintf' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'sprintf(\'TEST%s\', 1)'});" should be "TEST1"

  Scenario: Test PHP Function 'substr' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'substr(\'abcdef\', -1)'});" should be "f"

  Scenario: Test PHP Function 'strlen' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'strlen(\'abcdef\')'});" should be "6"

  Scenario: Test PHP Function 'str_replace' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'str_replace(\'aa\', \'bb\', \'aacc\')'});" should be "bbcc"

  Scenario: Test PHP Function 'strtolower' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'strtolower(\'AA\')'});" should be "aa"

  Scenario: Test PHP Function 'strtoupper' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'strtoupper(\'aa\')'});" should be "AA"

  Scenario: Test PHP Function 'trim' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'trim(\' aa \')'});" should be "aa"

  Scenario: Test PHP Function 'ltrim' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'ltrim(\' aa \')'});" should be "aa "

  Scenario: Test PHP Function 'rtrim' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'rtrim(\' aa \')'});" should be " aa"

  Scenario: Test PHP Function 'ucfirst' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'ucfirst(\'ucfirst\')'});" should be "Ucfirst"

  Scenario: Test PHP Function 'lcfirst' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'lcfirst(\'Lcfirst\')'});" should be "lcfirst"

  Scenario: Test PHP Function 'ucwords' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'ucwords(\'uppercase words\')'});" should be "Uppercase Words"

  Scenario: Test PHP Function 'wordwrap' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'wordwrap(\'The quick brown fox jumped over the lazy dog.\', 20, \'-\')'});" should be "The quick brown fox-jumped over the lazy-dog."

  Scenario: Test PHP Function 'number_format' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'number_format(1234.56, 2, \',\', \' \')'});" should be "1 234,56"

  Scenario: Test PHP Function 'strip_tags' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'strip_tags(\'<div>foo-bar</div>\')'});" should be "foo-bar"

  Scenario: Test PHP Function 'strrev' with expression language placeholder
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'strrev(\'foo-bar\')'});" should be "rab-oof"
