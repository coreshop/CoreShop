# Stores

CoreShop provides a straightforward API for managing stores within its system, allowing for the creation, reading,
updating, and deletion of store entities.

## Create

To create a new Store via API:

```php
$newStore = $container->get('coreshop.factory.store')->createNew();
```

Once you have instantiated a new Store, persist it as follows:

```php
$container->get('coreshop.manager.store')->persist($newStore);
$container->get('coreshop.manager.store')->flush();
```

You now have a newly persisted Store in your system.

## Read

To query for existing Stores:

```php
$storeRepository = $container->get('coreshop.repository.store');
$queryBuilder = $storeRepository->createQueryBuilder('c');
// Create your query
// Retrieve the result
$stores = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing Store:

```php
// Fetch the Store
$store = $storeRepository->findById(1);
$store->setName('Euro');
// Persist changes
$container->get('coreshop.manager.store')->persist($store);
$container->get('coreshop.manager.store')->flush();
```

## Delete

To delete an existing Store:

```php
// Fetch the Store
$store = $storeRepository->findById(1);
// Remove the Store
$container->get('coreshop.manager.store')->remove($store);
$container->get('coreshop.manager.store')->flush();
```

