# Zones

## Create
If you want to create a Zone via API, you can do following:

```php
$newZone = $container->get('coreshop.factory.zone')->createNew();
```

Now you have a new Zone, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.zone')->persist($newZone);
$container->get('coreshop.manager.zone')->flush();
```

You now have a new persisted Zone.

## Read

If you want to query for Zones, you can do following:

```php
$zoneRepository = $container->get('coreshop.repository.zone');

$queryBuilder = $zoneRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$zones = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Zone, you need to do following:

```php
// Fetch Zone

$zone = $zoneRepository->findById(1);
$zone->setName('Euro');

// And Persist it
$container->get('coreshop.manager.zone')->persist($zone);
$container->get('coreshop.manager.zone')->flush();
```

## Delete
If you want to update and existing Zone, you need to do following:

```php
// Fetch Zone

$zone = $zoneRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.zone')->remove($zone);
$container->get('coreshop.manager.zone')->flush();
```