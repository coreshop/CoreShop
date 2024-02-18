# Storage List Bundle

The Storage List Bundle assists in managing lists and collections of objects, such as carts, wishlists, or compare
lists, in CoreShop.

## Installation Process

To install the Storage List Bundle, use Composer:

```bash
$ composer require coreshop/storage-list-bundle:^4.0
```

## Usage

The bundle requires three models to function effectively:

1. **Storage List** - Represents the collection. Implement
   the [`StorageListInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListInterface.php).
2. **Storage Item** - The item within the collection, which can store additional information, such as prices for a cart.
   Implement
   the [`StorageListItemInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListItemInterface.php).
3. **Storage Product** - The actual product being stored inside the item. Implement
   the [`StorageListProductInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListProductInterface.php).

CoreShop provides basic implementations
of [`Storage List`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageList.php)
and [`Storage Item`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageItem.php).
You will need to implement the StorageListProduct yourself.

### Mutating Lists

Use
the [`Storage List Modifier`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/StorageListModifier.php)
to create and persist lists.

### Example: Basic Session-Based Wishlist

Let's create a session-based wishlist with the following components:

- Factory class for the Wishlist
- Factory class for the Wishlist Item
- StorageListManager (repository-like)
- StoreListModifier

CoreShop provides basic classes for these components:

```php
use Symfony\Component\HttpFoundation\Session\Session;

use CoreShop\Component\StorageList\Model\StorageList;
use CoreShop\Component\StorageList\Model\StorageListItem;
use CoreShop\Component\Resource\Factory\Factory;
use CoreShop\Component\StorageList\SessionStorageManager;
use CoreShop\Component\StorageList\SessionStorageListModifier;

$session = new Session();
$session->start();

$wishlistFactory = new Factory(StorageList::class);
$wishlistItemFactory = new Factory(StorageListItem::class);

$wishlistManager = new SessionStorageManager($session, 'wishlist', $wishlistFactory);
$wishlistModifier = new SessionStorageListModifier($wishlistItemFactory, $wishlistManager);

// Adding data to our List
$list = $wishlistManager->getStorageList();
$product = $productRepository->find(1); // Assumes StorageListProductInterface implementation

$listItem = $wishlistItemFactory->createNew();
$listItem->setProduct($product);
$listItem->setQuantity($quantity);

// Adding a Product
$wishlistModifier->addToList($list, $listItem);

// Removing a Product
$wishlistModifier->removeFromList($list, $listItem);
```

This bundle simplifies the management of various storage lists within CoreShop, enhancing the functionality and user
experience of your e-commerce platform.
