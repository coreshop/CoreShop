# CoreShop Storage List Bundle

Storage List Component helps you with Lists/Collections of Objects like a Cart, Wishlist or Compare List.

## Usage
To use it you need to have 3 models:

- a Storage List: the collection ([```CoreShop\Component\StorageList\Model\StorageListInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListInterface.php))
- a Storage Item: the item within the collection which could store additional information (eg. prices for a cart) ([```CoreShop\Component\StorageList\Model\StorageListItemInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListItemInterface.php))
- a Storage Product: the actual product (eg. object) being stored inside the Item. ([```CoreShop\Component\StorageList\Model\StorageListProductInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageListProductInterface.php))

The component already provides you with a basic implementation of [```Storage List```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageList.php) and [```Storage Item```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/Model/StorageItem.php).
You need to implement the StorageListProduct yourself.

To now mutate lists, the component gives you a [```Storage List Modifier```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/StorageList/StorageListModifier.php), which takes care about creating and persisting the List.

## Basic usage, Wishist example
For now, lets create a very basic Session based Wishlist:

We need to have following things:

- A Factory class for the Wishlist
- A Factory class for the Wishlist Item
- A StorageListManager to get the current list (more like a repository actually)
- A StoreListModifier

CoreShop gives you Basic classes for these 4 things, we just need to instantiate them:

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

//Now we can start putting data into our List
$list = $wishlistManager->getStorageList();

//Fetch our Product which implements CoreShop\Component\StorageList\Model\StorageListProductInterface
$product = $productRepository->find(1);


$listItem = $wishlistItemFactory->createNew();
$listItem->setProduct($product);
$listItem->setQuantity($quantity);

//Lets add our Product
$wishlistModifier->addToList($list, $listItem);

//If we now want to remove it, we can either use the $listItem, or the Product
//To do that with our item, we simply call
$wishlistModifier->removeFromList($list, $listItem);

```
