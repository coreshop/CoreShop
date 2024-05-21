# Tax Rules

Managing tax rules in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Create

To create a new tax rule via the API:

```php
$newTaxRule = $container->get('coreshop.factory.tax_rule')->createNew();
```

After creating a new Tax Rule instance, persist it using:

```php
$container->get('coreshop.manager.tax_rule')->persist($newTaxRule);
$container->get('coreshop.manager.tax_rule')->flush();
```

You now have a new persisted tax rule.

## Read

To query for tax rules:

```php
$ruleRepository = $container->get('coreshop.repository.tax_rule');
$queryBuilder = $ruleRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$rules = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing tax rule:

```php
// Fetch Tax Rule
$rule = $ruleRepository->findById(1);
$rule->setName('Euro');
// And Persist it
$container->get('coreshop.manager.tax_rule')->persist($rule);
$container->get('coreshop.manager.tax_rule')->flush();
```

## Delete

To delete an existing tax rule:

```php
// Fetch Tax Rule
$rule = $ruleRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.tax_rule')->remove($rule);
$container->get('coreshop.manager.tax_rule')->flush;
```
