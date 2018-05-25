@placeholder @placeholder_expression
Feature: In order to extend Pimcore's placeholder
  CoreShop adds a expression placeholder

  Scenario: Test a simple arithmetic expression
    Then the placeholder value for expression "%Expression(expression, {'expression' : '1+1'});" should be "2"

  Scenario: Test a container parameter expression
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'parameter(\'kernel.environment\')'});" should be "test"

  Scenario: Test a container service expression
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'service(\'coreshop.money_formatter\').format(100, \'EUR\', \'en\')'});" should be "€1.00"

  Scenario: Test a coreshop expression language provider for object
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'object(1)'});" should be "/"

  Scenario: Test a coreshop expression language provider for asset
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'asset(1)'});" should be "/"

  Scenario: Test a coreshop expression language provider for document
    Then the placeholder value for expression "%Expression(expression, {'expression' : 'document(1)'});" should be "/"
