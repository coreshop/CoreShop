# Tax Rates

Managing tax rates in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Create

To create a new tax rate via the API:

```php
$newTaxRate = $container->get('coreshop.factory.tax_rate')->createNew();
```

After creating a new Tax Rate instance, persist it using:

```php
$container->get('coreshop.manager.tax_rate')->persist($newTaxRate);
$container->get('coreshop.manager.tax_rate')->flush();
```

You now have a new persisted tax rate.

## Read

To query for tax rates:

```php
$rateRepository = $container->get('coreshop.repository.tax_rate');
$queryBuilder = $rateRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$rates = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing tax rate:

```php
// Fetch Tax Rate
$rate = $rateRepository->findById(1);
$rate->setName('Euro');
// And Persist it
$container->get('coreshop.manager.tax_rate')->persist($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```

## Delete

To delete an existing tax rate:

```php
// Fetch Tax Rate
$rate = $rateRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.tax_rate')->remove($rate);
$container->get('coreshop.manager.tax_rate')->flush();
```
