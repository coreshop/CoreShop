# Tax Rates

## Create
If you want to create a Zone via API, you can do following:

```php
$newZone = $container->get('coreshop.factory.tax_rate')->createNew();
```

Now you have a new Zone, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.tax_rate')->persist($newZone);
$container->get('coreshop.manager.tax_rate')->flush();
```

You now have a new persisted Zone.

## Read

If you want to query for Tax Rates, you can do following:

```php
$rateRepository = $container->get('coreshop.repository.tax_rate');

$queryBuilder = $rateRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$rates = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Zone, you need to do following:

```php
// Fetch Zone

$rate = $rateRepository->findById(1);
$rate->setName('Euro');

// And Persist it
$container->get('coreshop.manager.tax_rate')->persist($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```

## Delete
If you want to update and existing Zone, you need to do following:

```php
// Fetch Zone

$rate = $rateRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.tax_rate')->remove($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```