# Custom Customer Management

CoreShop utilizes Pimcore Data Objects to manage customer information, enhancing it with a wrapper for increased
dynamism and configurability. This approach leverages the Factory and Repository patterns for efficient data handling.

## Create

To create a new customer, you need to use the Factory Service:

```php
$customerFactory = $container->get('coreshop.factory.customer');
$customer = $customerFactory->createNew();
```

After creating the customer object, you can set the necessary values.

To save the new customer:

```php
$customer->save();
```

## Read

For retrieving customers, CoreShop provides a Repository Service:

```php
$repository = $container->get('coreshop.repository.customer');

// Query by ID
$customerWithIdOne = $repository->findById(1);

// Get a Listing as you would in Pimcore
$list = $repository->getList();
$list->setCondition("active = 1");
$customers = $list->getObjects();
```

## Update

Updating customer information follows the same process as in Pimcore:

```php
$repository = $container->get('coreshop.repository.customer');

// Query by ID
$customerWithIdOne = $repository->findById(1);

// Modify values
$customerWithIdOne->setName('test');
$customerWithIdOne->save();
```

## Delete

The deletion process is also similar to standard Pimcore operations:

```php
$repository = $container->get('coreshop.repository.customer');

// Query by ID
$customerWithIdOne = the repository->findById(1);
$customerWithIdOne->delete();
```