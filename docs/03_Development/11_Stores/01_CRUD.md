# CoreShop Stores

## Create
If you want to create a Store via API, you can do following:

```php
$newStore = $container->get('coreshop.factory.store')->createNew();
```

Now you have a new Store, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.store')->persist($newStore);
$container->get('coreshop.manager.store')->flush();
```

You now have a new persisted Store.

## Read

If you want to query for Stores, you can do following:

```php
$storeRepository = $container->get('coreshop.repository.store');

$queryBuilder = $storeRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$stores = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing Store, you need to do following:

```php
// Fetch Store

$store = $storeRepository->findById(1);
$store->setName('Euro');

// And Persist it
$container->get('coreshop.manager.store')->persist($store);
$container->get('coreshop.manager.store')->flush();
```

## Delete
If you want to update and existing Store, you need to do following:

```php
// Fetch Store

$store = $storeRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.store')->remove($store);
$container->get('coreshop.manager.store')->flush();
```

## Working with stores and pimcore objects
Note that if you are working with pimcore objects and the coreShopStore fieldtype you can not set the store but instead you have to set the ID of the store.
```php
// Fetch Store

$store = $storeRepository->findById(1);
$customer = \Pimcore\Model\DataObject\CoreShopCustomer::getById(2);
$customer->setCustomCoreShopStoreField($store->getId());

```
