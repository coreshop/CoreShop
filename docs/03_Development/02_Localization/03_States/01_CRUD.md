# States

Managing states in CoreShop involves various operations, including create, read, update, and delete. Below are the
guidelines for each of these operations.

## Create

To create a new state via the API:

```php
$newState = $container->get('coreshop.factory.state')->createNew();
```

After creating a new State instance, persist it using:

```php
$container->get('coreshop.manager.state')->persist($newState);
$container->get('coreshop.manager.state')->flush();
```

You now have a new persisted state.

## Read

To query for states:

```php
$stateRepository = $container->get('coreshop.repository.state');
$queryBuilder = $stateRepository->createQueryBuilder('c');
// You can now create your query
// And get the result
$states = $queryBuilder->getQuery()->getResult();
```

## Update

To update an existing state:

```php
// Fetch State
$state = $stateRepository->findById(1);
$state->setName('Euro');
// And Persist it
$container->get('coreshop.manager.state')->persist($state);
$container->get('coreshop.manager.state')->flush();
```

## Delete

To delete an existing state:

```php
// Fetch State
$state = $stateRepository->findById(1);
// And Remove it
$container->get('coreshop.manager.state')->remove($state);
$container->get('coreshop.manager.state')->flush();
```
