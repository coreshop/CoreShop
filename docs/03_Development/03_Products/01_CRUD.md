# Product

CoreShop leverages Pimcore Data Objects for persisting Product Information but adds a layer of dynamism and
configurability through a Factory and Repository Pattern.

## Create

To create a new product, first obtain the Factory Service:

```php
$productFactory = $container->get('coreshop.factory.product');
$product = $productFactory->createNew();
```

After setting the necessary values on the product, save it:

```php
$product->save();
```

## Read

Use the Repository Service provided by CoreShop to retrieve products:

```php
$repository = $container->get('coreshop.repository.product');

// Query by ID
$productWithIdOne = $repository->findById(1);

// Get a Listing as in Pimcore
$list = $repository->getList();
$list->setCondition("active = 1");
$products = $list->getObjects();
```

## Update

Updating a product follows the same pattern as in Pimcore:

```php
$repository = $container->get('coreshop.repository.product');

// Query by ID
$productWithIdOne = $repository->findById(1);

// Change values
$productWithIdOne->setName('test');
$productWithIdOne->save();
```

## Delete

Deleting a product also follows the familiar Pimcore process:

```php
$repository = $container->get('coreshop.repository.product');

// Query by ID
$productWithIdOne = $repository->findById(1);
$productWithIdOne->delete();
```
