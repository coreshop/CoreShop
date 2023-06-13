# CoreShop Countries

## Create
If you want to create a Country via API, you can do following:

```php
$newCountry = $container->get('coreshop.factory.country')->createNew();
```

Now you have a new Country, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.country')->persist($newCountry);
$container->get('coreshop.manager.country')->flush();
```

You now have a new persisted Country.

## Read

If you want to query for Countries, you can do following:

```php
$countryRepository = $container->get('coreshop.repository.country');

$queryBuilder = $countryRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$countries = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Country, you need to do following:

```php
// Fetch Country

$country = $countryRepository->findById(1);
$country->setName('Euro');

// And Persist it
$container->get('coreshop.manager.country')->persist($country);
$container->get('coreshop.manager.country')->flush();
```

## Delete
If you want to update and existing Country, you need to do following:

```php
// Fetch Country

$country = $countryRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.country')->remove($country);
$container->get('coreshop.manager.country')->flush();
```