# Tax Rules

## Create
If you want to create a Tax Rule via API, you can do following:

```php
$newTaxRule = $container->get('coreshop.factory.tax_rule')->createNew();
```

Now you have a new Tax Rule, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.tax_rule')->persist($newTaxRule);
$container->get('coreshop.manager.tax_rule')->flush();
```

You now have a new persisted Tax Rule.

## Read

If you want to query for Tax Rules, you can do following:

```php
$ruleRepository = $container->get('coreshop.repository.tax_rule');

$queryBuilder = $ruleRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$rules = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Tax Rule, you need to do following:

```php
// Fetch Tax Rule

$rule = $ruleRepository->findById(1);
$rule->setName('Euro');

// And Persist it
$container->get('coreshop.manager.tax_rule')->persist($rule);
$container->get('coreshop.manager.tax_rule')->flush();
```

## Delete
If you want to update and existing Tax Rule, you need to do following:

```php
// Fetch Tax Rule

$rule = $ruleRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.tax_rule')->remove($rule);
$container->get('coreshop.manager.tax_rule')->flush();
```
