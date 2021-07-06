# Within 2.1

## 2.1.9
 - Bugs:
    - [OrderBundle] fix usage of inherited values in backend-cart/order controllers (https://github.com/coreshop/CoreShop/pull/1461, https://github.com/coreshop/CoreShop/issues/1459)
    
## 2.1.8
 - Bugs:
    - [ResourceBundle] Fix unique entity validator (https://github.com/coreshop/CoreShop/pull/1385)
    - [DataHub] fix integration (https://github.com/coreshop/CoreShop/pull/1389)
   
 - Features:
    - [WorkflowBundle] Add enabled option to workflow callbacks (https://github.com/coreshop/CoreShop/pull/1392)
    
## 2.1.7
 - Bugs:
    - [Pimcore] fix getDataForEditmode (https://github.com/coreshop/CoreShop/pull/1372, https://github.com/coreshop/CoreShop/pull/1361)

## 2.1.6
 - Bugs:
    - [CoreBundle][2.1] Fix name of country combo (https://github.com/coreshop/CoreShop/pull/1350)

## 2.1.5
 - Bugs:
    - [Translations] fix: add missing translation (https://github.com/coreshop/CoreShop/pull/1308)
    - [IndexBundle] index ui improvements (https://github.com/coreshop/CoreShop/pull/1300)
    - [ThemeBundle] fix configuration for default resolvers (https://github.com/coreshop/CoreShop/pull/1301)
    - [AddressBundle] introduce filter-active action and filter store-base countries by those (https://github.com/coreshop/CoreShop/pull/1302)
    
## 2.1.4
 - Feature:
    - [CoreBundle] allow store-values to be reset and inherit again (https://github.com/coreshop/CoreShop/pull/1273)
 - Bugs:
    - [IndexBundle] Change return type of WorkerInterface::getList (https://github.com/coreshop/CoreShop/pull/1280)
    - [ThemeBundle] fix default theme-resolvers (https://github.com/coreshop/CoreShop/pull/1281)
    - [Pimcore] make compatible with Pimcore 6.5 (https://github.com/coreshop/CoreShop/pull/1285)
    - [Core] fix bug where we calculated item-discount and item-discount-prices (https://github.com/coreshop/CoreShop/pull/1293)

## 2.1.3
 - Bugs:
    - [Address, Order, Core] fix release (https://github.com/coreshop/CoreShop/pull/1269)

## 2.1.2
 - Features:
    - [FrontendBundle] Change function from private to protected (https://github.com/coreshop/CoreShop/pull/1248)
    - [Installer] update logo (https://github.com/coreshop/CoreShop/pull/1264)
    - [Cart] introduce cart-context resolver to allow better extendability of the context used for the cart (https://github.com/coreshop/CoreShop/pull/1267)
        
 - Bugs:
    - [ThemeBundle] fix Undefined index: default_resolvers (https://github.com/coreshop/CoreShop/pull/1235)
    - [IndexBundle]  $indexIds is always an array, hence the condition is now empty (https://github.com/coreshop/CoreShop/pull/1241)
    - [Stan] fixes for 2.1 (https://github.com/coreshop/CoreShop/pull/1244)
    - [CurrencyBundle] fix money-currency type is rounding prices wrong (https://github.com/coreshop/CoreShop/pull/1238)
    - [CoreBundle] Change repository so that unit definition deletion works with multiple product models (https://github.com/coreshop/CoreShop/pull/1252)
    - [Order] fix throwing/catching right exceptions in purchasable calculalator (https://github.com/coreshop/CoreShop/pull/1250)
    - [Core] allow non QuantityPriceRangeAware Products in cart-processor (https://github.com/coreshop/CoreShop/pull/1249)
    - [IndexBundle] fix index columns form (https://github.com/coreshop/CoreShop/pull/1259)
    - [Country] don't call the request based resolvers every time (https://github.com/coreshop/CoreShop/pull/1261)

## 2.1.1
 - Features:
    - [CoreBundle] Implement Variant Unit and QPR Solidifier (https://github.com/coreshop/CoreShop/issues/1157)
    - [AddressBundle] Improve Country Address Formatting (https://github.com/coreshop/CoreShop/pull/1153)
    - [OrderBundle] properly implement AddMultipleToCart (https://github.com/coreshop/CoreShop/pull/1154)
    - [IndexBundle] allow to query relations also by relation type (https://github.com/coreshop/CoreShop/pull/1156)
    - [SEOBundle] add priority to extractors (https://github.com/coreshop/CoreShop/pull/1155)
    - [QuantityPriceRules] Allow Object Deletion without removing QPR first (https://github.com/coreshop/CoreShop/issues/1160)
    - [CoreBundle] Improve Unit Definition <=> QPR Dependency (https://github.com/coreshop/CoreShop/pull/1161)
    - [StorageList] introduce service to resolve if cart-items are equal (https://github.com/coreshop/CoreShop/pull/1188)
    - [OrderBundle] Allow Item Data per Row in Order Overview (https://github.com/coreshop/CoreShop/pull/1193)
    - [OrderBundle] add sale-detail event (https://github.com/coreshop/CoreShop/pull/1192)
    - [IndexBundle] Argument for re-index command (https://github.com/coreshop/CoreShop/pull/1219)
    - [CoreBundle] Maximum Quantity to Order (https://github.com/coreshop/CoreShop/issues/1209)
        
 - Bugs:
    - [StoreBundle] add missing store dependency (https://github.com/coreshop/CoreShop/pull/1159)
    - [Install] add dummy migration (https://github.com/coreshop/CoreShop/pull/1172)
    - [CoreBundle] Remove Store Values after Store has been removed (https://github.com/coreshop/CoreShop/pull/1171)
    - [Order] Fix Character Length Count in Voucher Code Generator (https://github.com/coreshop/CoreShop/pull/1194/files)
    - [Order] fix item price for items without tax-rule (https://github.com/coreshop/CoreShop/pull/1200)
    - [TRACKING] use single item price in order item extractor (https://github.com/coreshop/CoreShop/pull/1232)
    
## 2.1.0
 - If you have a custom validation File for *AddToCart* or *Cart*, make sure to use the new 2 MinimumQuantity and MaximumQuantity Constraints. Otherwise it will happen that a validation is triggered twice.
   
## 2.1.0
 - Bugs:
   - [ThemeBundle] add missing dependency to pimcore-bundle (https://github.com/coreshop/CoreShop/pull/1138, https://github.com/coreshop/CoreShop/pull/1140)
   - [ResourceBundle] fix naming of parameter sortBy (https://github.com/coreshop/CoreShop/pull/1132)
   - [Quantity Price Rules] Check Inherited Product Quantity Price Range Data (https://github.com/coreshop/CoreShop/pull/1143)
   - [FrontendBundle] allow usage of auto-wired Frontend Controllers (https://github.com/coreshop/CoreShop/pull/1141)
   - [OrderBundle] CartItem Quantity has to be > 0 (https://github.com/coreshop/CoreShop/pull/1144)

## 2.1.0-rc.2
 - Features:
   - [IndexBundle] allow for more complex doctrine types in index (https://github.com/coreshop/CoreShop/pull/1110)
   - [IndexBundle] add Select and Multiselect Filter Processor from Multiselect (https://github.com/coreshop/CoreShop/pull/1111)
   - [Autowire] improvement: allow service registries to be autowired (https://github.com/coreshop/CoreShop/pull/1113, https://github.com/coreshop/CoreShop/pull/1116, https://github.com/coreshop/CoreShop/pull/1122)
   - [QuantityPriceRules] allow price range ordering @solverat (https://github.com/coreshop/CoreShop/pull/1121)
   - [Payment] Fix payment provider logo @davidhoeck (https://github.com/coreshop/CoreShop/pull/1124)
   - [ResourceBundle, OrderBundle, CoreBundle] introduce more memory efficient (https://github.com/coreshop/CoreShop/pull/1126, https://github.com/coreshop/CoreShop/pull/1129)
   - [Shipping, ShippingBundle] Add logo field to carrier @davidhoeck (https://github.com/coreshop/CoreShop/pull/1127)
   - 

 - Bugs:
   - [Tests] exit code (https://github.com/coreshop/CoreShop/pull/1119)
   - [Tracking] remove decimal factor multiplier in order extractor @solverat (https://github.com/coreshop/CoreShop/pull/1128)
   - [QuantityPriceRules] Move migration for Quantity Rule Range Unit to Sales Unit migration  (https://github.com/coreshop/CoreShop/pull/1123)
   s
## 2.1.0
 - BC-Break: Introduced `array $options` parameter into `CoreShop\Component\Index\Listing\ListingInterface` to allow certain variations for loading data
  
 - Introduced WholesalePrice Calculators, this deprecates the "wholesalePrice" property in the Product Class and adds the "wholesaleBuyingPrice" Property with a currency attached. We've added a migration for that, but since we need a currency now, we just assume the buying currency as the defaults store currency. If you have a different one, create a custom migration that changes it.

 - `CoreShop\Component\StorageList\StorageListModifierInterface` got completely refactored and works a bit different now. Since deciding what StorageListItem belongs to what product, can be a bit more complicated, we decided to introduce a BC break.
   - `CoreShop\Component\StorageList\StorageListModifierInterface` added `addToList` function
   - `CoreShop\Component\StorageList\StorageListModifierInterface` removed `remove` to `removeFromList`
   - `CoreShop\Component\StorageList\Model\StorageListItemInterface` added `equals` function
   - `CoreShop\Component\StorageList\Model\StorageListInterface` removed `getItemForProduct` function
   - `CoreShop\Component\StorageList\Model\StorageListProductInterface` got deprecated, since not it's not needed anymore
 - `CoreShop\Component\Order\Factory\CartItemFactoryInterface` introduced a new function `public function createWithPurchasable(PurchasableInterface $purchasable, $quantity = 1);`

 - Introduced Theme-Bundle to handle Themes (https://github.com/coreshop/CoreShop/pull/749, https://github.com/coreshop/CoreShop/pull/756, https://github.com/coreshop/CoreShop/pull/755)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelper.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelper.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelperInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelperInterface.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolver.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolver.php)
   - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolverInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolverInterface.php)
   
 - Introduce AddToCartFormType and QuantityType. This allows to use validators to check if its allowed to add a product to the cart. If you update from CoreShop 2.0.* change the add-to-cart form in your templates to the following: (https://github.com/coreshop/CoreShop/pull/812/files#diff-3e06a5f0e813be230a0cd232e916738eL29) (https://github.com/coreshop/CoreShop/pull/812, https://github.com/coreshop/CoreShop/pull/864)
   - `{{ render(url('coreshop_cart_add', {'product': product.id})) }}` 
   - Be sure you have adopted the new form template in `views/Product/_addToCart.html.twig`
   
 - Introduced Store Unit: (https://github.com/coreshop/CoreShop/pull/877, https://github.com/coreshop/CoreShop/pull/883, https://github.com/coreshop/CoreShop/pull/950, https://github.com/coreshop/CoreShop/pull/902, https://github.com/coreshop/CoreShop/pull/896)
    - Please add `product_unit` to permission table.
    - Remove `storePrice` field from all product classes
    - If you don't use the `Store Price` element in your classes besides the `storePrice` field, you should delete the `coreshop_product_store_price` table after migration.   
    - We introduced a new jquery plugin `$.coreshopQuantitySelector()` which allows you to add more numeric control to your quantity field, checkout our demo [example](https://github.com/coreshop/CoreShop/blob/7d05ccd89aed99f9fd93c585e096cd1edaf20010/src/CoreShop/Bundle/FrontendBundle/Resources/public/static/js/shop.js#L20).

-  Features:
    - *Product Quantity Rules*, big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/791, https://github.com/coreshop/CoreShop/pull/954, https://github.com/coreshop/CoreShop/pull/951, https://github.com/coreshop/CoreShop/pull/898, https://github.com/coreshop/CoreShop/pull/813) 
    - *Product Units*, big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/861, https://github.com/coreshop/CoreShop/pull/911, https://github.com/coreshop/CoreShop/pull/900, https://github.com/coreshop/CoreShop/pull/897, https://github.com/coreshop/CoreShop/pull/891, https://github.com/coreshop/CoreShop/pull/875)
    - *Minimum Order Quantity and Item Quantity Factor* big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/881)
    - Introduce Menu Bundle (https://github.com/coreshop/CoreShop/pull/854, https://github.com/coreshop/CoreShop/pull/880, https://github.com/coreshop/CoreShop/pull/878, https://github.com/coreshop/CoreShop/pull/876)
    - Introduce Theme Bundle (https://github.com/coreshop/CoreShop/pull/749, https://github.com/coreshop/CoreShop/pull/756, https://github.com/coreshop/CoreShop/pull/755)
    - [Store Values] swap store-prices with store-values and make them extendable for custom store values (https://github.com/coreshop/CoreShop/pull/877, https://github.com/coreshop/CoreShop/pull/883, https://github.com/coreshop/CoreShop/pull/950, https://github.com/coreshop/CoreShop/pull/902, https://github.com/coreshop/CoreShop/pull/896)
    - [ResourceBundle] Add group to ThumbnailInstaller (https://github.com/coreshop/CoreShop/pull/1017) @AndiKeiser
    - [All] AutoWiring (https://github.com/coreshop/CoreShop/pull/850)
    - [All] Pimcore 6/Symfony 4 compatibilty (https://github.com/coreshop/CoreShop/pull/996, https://github.com/coreshop/CoreShop/pull/1062, https://github.com/coreshop/CoreShop/pull/1035)
    - [Tests] Update to friends-of-behat/symfony-extension:^2.0 (https://github.com/coreshop/CoreShop/pull/1024)
    - [Travis] add setup for Pimcore 6 changed system.yml config (https://github.com/coreshop/CoreShop/pull/1029)
    - [All] Introduce configurable decimal precision and factor (https://github.com/coreshop/CoreShop/pull/1030)
    - [All] change db type for pricing fields to BIGINT (https://github.com/coreshop/CoreShop/pull/1032, https://github.com/coreshop/CoreShop/pull/1098)
    - [ShippingBundle] introduce gross/net checkbox for amount condition (https://github.com/coreshop/CoreShop/pull/1042)
    - [Graphql] Add Support for Pimcore GraphQl Data Hub (https://github.com/coreshop/CoreShop/pull/1052)
    - [Tracking] Add Decimal Precision to Order Extractor (https://github.com/coreshop/CoreShop/pull/1058)
    - [MoneyBundle] add fallback in money-bundle for decimal precision (https://github.com/coreshop/CoreShop/pull/1061)
    - [Docs] Added visualization for coreshop_order Workflow (https://github.com/coreshop/CoreShop/pull/1067) @davidhoeck
    - [Pimcore] add conflict for Pimcore 6.1.0 and Pimcore 6.1.1 (https://github.com/coreshop/CoreShop/pull/1069)
    - [Docs] Update 03_Theme.md (https://github.com/coreshop/CoreShop/pull/1072) @D37R4C7
    - [FQCN] FQCN Services (https://github.com/coreshop/CoreShop/pull/1079, https://github.com/coreshop/CoreShop/pull/1084, https://github.com/coreshop/CoreShop/issues/1085, https://github.com/coreshop/CoreShop/pull/1086, https://github.com/coreshop/CoreShop/pull/1090)
    - [Product] Unit Definition - Precision (https://github.com/coreshop/CoreShop/pull/1081, https://github.com/coreshop/CoreShop/pull/1091, https://github.com/coreshop/CoreShop/pull/1092) @solverat
    - [IndexBundle] split conditions into pre_conditions and user_conditions (https://github.com/coreshop/CoreShop/pull/1055)
    - [Quantity Price Rules] Remove "to" field from quantity price range (https://github.com/coreshop/CoreShop/pull/1003, https://github.com/coreshop/CoreShop/pull/1095)
    - [Order] Introduce backend cart-creation and cart-details (https://github.com/coreshop/CoreShop/pull/963)
    - [PermissionSetup] add category to Permission (https://github.com/coreshop/CoreShop/pull/1101)
    - [ShippingBundle] add more carrier price options (https://github.com/coreshop/CoreShop/pull/1015)
    - [FrontendBundle] show discount/surcharge label in order overview (https://github.com/coreshop/CoreShop/pull/1006)
    - [Index, IndexBundle] allow options for the listing load function (https://github.com/coreshop/CoreShop/pull/1001)
    - [ResourceBundle] add connection interface into Pimcore Repository (https://github.com/coreshop/CoreShop/pull/1000)
    - [ProductBundle, CoreBundle] set itemQuantityFactor min value to null (https://github.com/coreshop/CoreShop/pull/993)
    - [Core] use default unit quanity in onhold inventory (https://github.com/coreshop/CoreShop/pull/990) @solverat
    - [Maintenance] refactor to use new maintenance task from pimcore 5.8 (https://github.com/coreshop/CoreShop/pull/986)
    - [All] require min Pimcore 5.8 and PHP 7.2 (https://github.com/coreshop/CoreShop/pull/973)
    - [CoreBundle] introduce store-preview for products (https://github.com/coreshop/CoreShop/pull/982)
    - [Adjustments] remove return type AdjustmentInterface (https://github.com/coreshop/CoreShop/pull/978) @solverat
    - [Taxation] fix tax collection on gross values - 2.1 (https://github.com/coreshop/CoreShop/pull/974)
    - [Order] introduce translatable cart-price-rules (https://github.com/coreshop/CoreShop/pull/969)
    - [WholesaleCalculator] introduce purchasable wholesale calculator (https://github.com/coreshop/CoreShop/pull/957)
    - [Product] introduce stop propagation flag for price-rules (https://github.com/coreshop/CoreShop/pull/946)
    - [Pimcore] make CoreShop 2.1 compatible with Pimcore 5.7.2 (https://github.com/coreshop/CoreShop/pull/915)
    - [CoreBundle] serialize relational values (product and store) as relation (https://github.com/coreshop/CoreShop/pull/916)
    - [Core] Disable Customer Deletion if bounded Orders are available (https://github.com/coreshop/CoreShop/pull/732)
    - [PriceRules] add priority to product-price-rules (https://github.com/coreshop/CoreShop/pull/905)
    - [Product] add translation to product price rules (https://github.com/coreshop/CoreShop/pull/879)
    - [All] Min 5.7 (https://github.com/coreshop/CoreShop/pull/871)
    - [Core] Refactor how we identify CartItem - Product (https://github.com/coreshop/CoreShop/pull/866)
    - [Core/Cart] Refactor add to cart (https://github.com/coreshop/CoreShop/pull/864)
    - [Cart] implement add-to-cart as Symfony Form (https://github.com/coreshop/CoreShop/pull/812)
    - [Core, Order, Product] throw exceptions for when a price can't be found (https://github.com/coreshop/CoreShop/pull/811)
    - [Order] make accessor protected for OrderDocument Processor (https://github.com/coreshop/CoreShop/pull/775)
    - [WorkflowBundle, OrderBundle] always load all available coreshop states into js (https://github.com/coreshop/CoreShop/pull/773)
    - [Core, Order] also apply discounts to cart-items (https://github.com/coreshop/CoreShop/pull/770)
    - [OrderBundle, Pimcore] extract DataLoader from Controller to be used oustide (https://github.com/coreshop/CoreShop/pull/771)
    - [OrderBundle] add event to prepare sale in order to better extend details (https://github.com/coreshop/CoreShop/pull/772)
    - [IndexBundle] implement optional inclusion into ProcessManager (https://github.com/coreshop/CoreShop/pull/758)
 
- Bugs:
    - [Product] remove getIsAvailableWhenOutOfStock and setIsAvailableWhenOutOfStock (https://github.com/coreshop/CoreShop/pull/1019)
    - [CoreBundle] fix inheritance for store-values (https://github.com/coreshop/CoreShop/pull/1028)
    - [ResourceBundle] Fix missing coreshop.helper namespace (https://github.com/coreshop/CoreShop/pull/1039)
    - [PimcoreBundle] fix dynamic-dropdown for pimcore-6 (https://github.com/coreshop/CoreShop/pull/1040)
    - [All] fix related to element.href and this pimcore PR: pimcore/pimcore#4496 (https://github.com/coreshop/CoreShop/pull/1041)
    - [Core] Store Values - default value to 0 instead of null, fix setting inherited store values (https://github.com/coreshop/CoreShop/pull/1093)
    - [FrontendBundle] fix wishlist remove and allow purchasables (https://github.com/coreshop/CoreShop/pull/997)
    - [Bundles] provide proper version strings and names (https://github.com/coreshop/CoreShop/pull/970)
    - [Migration] only add indices to store_price table if table actually exists (https://github.com/coreshop/CoreShop/pull/967)
    - [IndexBundle] improve standalone usage (https://github.com/coreshop/CoreShop/pull/965)
    - [Product] allow price rule labels to be null and fix error with two trait constructors (https://github.com/coreshop/CoreShop/pull/953)
    - [ProductBundle] re-add active to list serializer group (https://github.com/coreshop/CoreShop/pull/912)
    - [CoreBundle] fix cart-stock validation (https://github.com/coreshop/CoreShop/pull/894)
    - [Tests] [Behat] the cart tests haven't been ran since the theme-bundle was introduced (https://github.com/coreshop/CoreShop/pull/872)
