# Within 2.1

## 2.1.0
 - `CoreShop\Component\StorageList\StorageListModifierInterface` got completely refactored and works a bit different now. Since deciding what StorageListItem belongs to what product, can be a bit more complicated, we decided to introduce a BC break.
   - `CoreShop\Component\StorageList\StorageListModifierInterface` added `addToList` function
   - `CoreShop\Component\StorageList\StorageListModifierInterface` removed `remove` to `removeFromList`
   - `CoreShop\Component\StorageList\Model\StorageListItemInterface` added `equals` function
   - `CoreShop\Component\StorageList\Model\StorageListInterface` removed `getItemForProduct` function
   - `CoreShop\Component\StorageList\Model\StorageListProductInterface` got deprecated, since not it's not needed anymore
 - `CoreShop\Component\Order\Factory\CartItemFactoryInterface` introduced a new function `public function createWithPurchasable(PurchasableInterface $purchasable, $quantity = 1);`

 - Introduced Theme-Bundle to handle Themes
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelper.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelper.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelperInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelperInterface.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolver.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolver.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolverInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolverInterface.php)
   
 - Introduce AddToCartFormType. This allows to use validators to check if its allowed to add a product to the cart. If you update from CoreShop 2.0.* change the add-to-cart form in your templates to the following: (https://github.com/coreshop/CoreShop/pull/812/files#diff-3e06a5f0e813be230a0cd232e916738eL29)
   ```
   {{ render(url('coreshop_cart_add', {'product': product.id})) }}
   ``` 
