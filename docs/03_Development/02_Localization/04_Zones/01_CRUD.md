# Zones

Managing zones in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Create

To create a new zone via the API:

```php
$newZone = $container->get('coreshop.factory.zone')->createNew();
```

After creating a new Zone instance, persist it using:

```php
$container->get('coreshop.manager.zone')->persist($newZone);
$container->get('coreshop.manager.zone')->flush();
```

You now have a new persisted zone.

## Read

To query for zones:

```php
$zoneRepository = $container->get('coreshop.repository.zone');
$queryBuilder = $zoneRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$zones = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing zone:

```php
// Fetch Zone
$zone = $zoneRepository->findById(1);
$zone->setName('Euro');
// And Persist it
$container->get('coreshop.manager.zone')->persist($zone);
$container->get('coreshop.manager.zone')->flush();
```

## Delete

To delete an existing zone:

```php
// Fetch Zone
$zone = $zoneRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.zone')->remove($zone);
$container->get('coreshop.manager.zone')->flush();
```
