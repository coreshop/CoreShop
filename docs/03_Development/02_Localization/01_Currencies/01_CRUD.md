# Currencies

## Create

If you want to create a Currency via API, you can do following:

```php
$newCurrency = $container->get('coreshop.factory.currency')->createNew();
```

Now you have a new Currency, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.currency')->persist($newCurrency);
$container->get('coreshop.manager.currency')->flush();
```

You now have a new persisted Currency.

## Read

If you want to query for Currencies, you can do following:

```php
$currencyRepository = $container->get('coreshop.repository.currency');

$queryBuilder = $currencyRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$currencies = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update an existing Currency, you need to do following:

```php
// Fetch Currency

$currency = $currencyRepository->findById(1);
$currency->setName('Euro');

// And Persist it
$container->get('coreshop.manager.currency')->persist($currency);
$container->get('coreshop.manager.currency')->flush();
```

## Delete

If you want to delete an existing Currency, you need to do following:

```php
// Fetch Currency

$currency = $currencyRepository->findById(1);

// And remove it
$container->get('coreshop.manager.currency')->remove($currency);
$container->get('coreshop.manager.currency')->flush();
```
