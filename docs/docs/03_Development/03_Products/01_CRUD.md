# CoreShop Product

CoreShop uses Pimcore Data Objects to persist Product Information. But, it adds a little wrapper around it to be more
dynamic and configurable. It uses a Factory and Repository Pattern to do that.

## Create

If you want to create a new Product, we need to get our Factory Service for that:

```php
$productFactory = $container->get('coreshop.factory.product');
$product = $productFactory->createNew();
```

No we have our product and we can set all needed values.

If you now want to save it, just call the save function

```php
$product->save();
```

## Read

To get products, you need to use the Repository Service CoreShop provides you.

```php
$repository = $container->get('coreshop.repository.product');


// Query by ID
$productWithIdOne = $repository->findById(1);

// Get a Listing how you know it from Pimcore
$list = $repository->getList();
$list->setCondition("active = 1");
$products = $list->getObjects();

```

## Update

Update works the same as you are used to in Pimcore

```php
$repository = $container->get('coreshop.repository.product');


// Query by ID
$productWithIdOne = $repository->findById(1);

// Change values
$productWithIdOne->setName('test');
$productWithIdOne->save();
```

## Delete

Delete works the same as you are used to in Pimcore

```php
$repository = $container->get('coreshop.repository.product');


// Query by ID
$productWithIdOne = $repository->findById(1);
$productWithIdOne->delete();
```
