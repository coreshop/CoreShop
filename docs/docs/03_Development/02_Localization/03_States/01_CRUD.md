# States

## Create
If you want to create a State via API, you can do following:

```php
$newState = $container->get('coreshop.factory.state')->createNew();
```

Now you have a new State, if you want to persist it, you need to do following:

```php
$container->get('coreshop.manager.state')->persist($newState);
$container->get('coreshop.manager.state')->flush();
```

You now have a new persisted State.

## Read

If you want to query for States, you can do following:

```php
$stateRepository = $container->get('coreshop.repository.state');

$queryBuilder = $stateRepository->createQueryBuilder('c');

// You can now create your query

// And get the result

$states = $queryBuilder->getQuery()->getResult();

```

## Update

If you want to update and existing State, you need to do following:

```php
// Fetch State

$state = $stateRepository->findById(1);
$state->setName('Euro');

// And Persist it
$container->get('coreshop.manager.state')->persist($state);
$container->get('coreshop.manager.state')->flush();
```

## Delete
If you want to update and existing State, you need to do following:

```php
// Fetch State

$state = $stateRepository->findById(1);

// And Persist it
$container->get('coreshop.manager.state')->remove($state);
$container->get('coreshop.manager.state')->flush();
```