# CoreShop Carrier

## Create
If you want to create a Carrier via API, you can do following:

```php
$newCarrier = $container->get('coreshop.factory.carrier')->createNew();
```

Now you have a new Carrier, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.carrier')->persist($newCarrier);
$container->get('coreshop.manager.carrier')->flush();
```

You now have a new persisted Carrier.

## Read

If you want to query for Carriers, you can do following:

```php
$carrierRepository = $container->get('coreshop.repository.carrier');

$queryBuilder = $carrierRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$carriers = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Carrier, you need to do following:

```php
// Fetch Carrier

$carrier = $carrierRepository->findById(1);
$carrier->setName('Euro');

// And Persist it
$container->get('coreshop.manager.carrier')->persist($carrier);
$container->get('coreshop.manager.carrier')->flush();
```

## Delete
If you want to update and existing Carrier, you need to do following:

```php
// Fetch Carrier

$carrier = $carrierRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.carrier')->remove($carrier);
$container->get('coreshop.manager.carrier')->flush();
```