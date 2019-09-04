# Within 2.1

## 2.1.0
 - BC-Break: `quantity` Field is now a `text` field instead of `number`:
   - We introduced a new jquery plugin `$.coreshopQuantitySelector()` which allows you to add more numeric control to your quantity field, checkout our demo [example](https://github.com/coreshop/CoreShop/blob/7d05ccd89aed99f9fd93c585e096cd1edaf20010/src/CoreShop/Bundle/FrontendBundle/Resources/public/static/js/shop.js#L20).
 
 - BC-Break: Introduced `array $options` parameter into `CoreShop\Component\Index\Listing\ListingInterface` to allow certain variations for loading data
  
 - Introduced WholesalePrice Calculators, this deprecates the "wholesalePrice" property in the Product Class and adds the "wholesaleBuyingPrice" Property with a currency attached. We've added a migration for that, but since we need a currency now, we just assume the buying currency as the defaults store currency. If you have a different one, create a custom migration that changes it.

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
   - `{{ render(url('coreshop_cart_add', {'product': product.id})) }}` 
   - Be sure you have adopted the new form template in `views/Product/_addToCart.html.twig`
   
 - Introduced Store Unit:
    - Please add `product_unit` to permission table.
    - Remove `storePrice` field from all product classes
    - If you don't use the `Store Price` element in your classes besides the `storePrice` field, you should delete the `coreshop_product_store_price` table after migration.
