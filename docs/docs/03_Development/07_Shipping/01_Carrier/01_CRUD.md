# Carrier

Managing carriers is an essential aspect of eCommerce logistics in CoreShop. This guide details how to create, read,
update, and delete carriers via the API.

## Create

To create a new carrier via the API:

```php
$newCarrier = $container->get('coreshop.factory.carrier')->createNew();
```

After creating a new Carrier instance, persist it using:

```php
$container->get('coreshop.manager.carrier')->persist($newCarrier);
$container->get('coreshop.manager.carrier')->flush();
```

You now have a newly persisted Carrier.

## Read

To query for carriers:

```php
$carrierRepository = $container->get('coreshop.repository.carrier');
$queryBuilder = $carrierRepository->createQueryBuilder('c');
// Create your query
// Get the result
$carriers = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing carrier:

```php
// Fetch Carrier
$carrier = $carrierRepository->findById(1);
$carrier->setName('DHL');
// Persist changes
$container->get('coreshop.manager.carrier')->persist($carrier);
$container->get('coreshop.manager.carrier')->flush();
```

## Delete

To delete an existing carrier:

```php
// Fetch Carrier
$carrier = $carrierRepository->findById(1);
// Remove Carrier
$container->get('coreshop.manager.carrier')->remove($carrier);
$container->get('coreshop.manager.carrier')->flush();
```
