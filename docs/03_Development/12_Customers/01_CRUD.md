# CoreShop Custom

CoreShop uses Pimcore Data Objects to persist Customer Information. But, it adds a little wrapper around it to be mire
dynamic and configurable. It uses a Factory and Repository Pattern to do that.

## Create

If you want to create a new Custom, we need to get our Factory Service for that:

```php
$customerFactory = $container->get('coreshop.factory.customer');
$customer = $customerFactory->createNew();
```

No we have our customer and we can set all needed values.

If you now want to save it, just call the save function

```php
$customer->save();
```

## Read

To get customers, you need to use the Repository Service CoreShop provides you.

```php
$repository = $container->get('coreshop.repository.customer');


// Query by ID
$customerWithIdOne = $repository->findById(1);

// Get a Listing how you know it from Pimcore
$list = $repository->getList();
$list->setCondition("active = 1");
$customers = $list->getObjects();

```

## Update

Update works the same as you are used to in Pimcore

```php
$repository = $container->get('coreshop.repository.customer');


// Query by ID
$customerWithIdOne = $repository->findById(1);

// Change values
$customerWithIdOne->setName('test');
$customerWithIdOne->save();
```

## Delete

Delete works the same as you are used to in Pimcore

```php
$repository = $container->get('coreshop.repository.customer');


// Query by ID
$customerWithIdOne = $repository->findById(1);
$customerWithIdOne->delete();
```
