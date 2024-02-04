a# Countries

In CoreShop, managing countries through the API involves several operations including create, read, update, and delete.
Below are the guidelines for each of these operations.

## Create

To create a new country via API:

```php
$newCountry = $container->get('coreshop.factory.country')->createNew();
```

After creating a new Country instance, persist it using:

```php
$container->get('coreshop.manager.country')->persist($newCountry);
$container->get('coreshop.manager.country')->flush();
```

You now have a new persisted country.

## Read

To query for countries:

```php
$countryRepository = $container->get('coreshop.repository.country');
$queryBuilder = $countryRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$countries = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing country:

```php
// Fetch Country
$country = $countryRepository->findById(1);
$country->setName('Euro');
// And Persist it
$container->get('coreshop.manager.country')->persist($country);
$container->get('coreshop.manager.country')->flush();
```

## Delete

To delete an existing country:

```php
// Fetch Country
$country = $countryRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.country')->remove($country);
$container->get('coreshop.manager.country')->flush();
```
