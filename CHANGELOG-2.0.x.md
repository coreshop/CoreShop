# Within V2

## 2.0.7
 - Bug:
  - [Purchasable] When using custom purchasable, CoreShop always assumed that your class has a weight, even though it didn't had to. That's why we moved all weight related fields to the Core Component and CoreBundle.
   

## 2.0.6:
 - Bug:
   - [Adjustments] in the classes "CoreShopOrderItem", "CoreShopQuoteItem", was a typo `basePdjustmentItems` instead of `baseAdjustmentItems`. Please manually rename them to `baseAdjustmentItems`
   - [ProductBundle] Fix extjs layout crash price rule is inactive (https://github.com/coreshop/CoreShop/pull/908)
   - [FrontendBundle] fix address creation redirect (https://github.com/coreshop/CoreShop/pull/910)
   - [StorageList] Storage List and Storage List Item is not a Pimcore Object (https://github.com/coreshop/CoreShop/pull/907)
   - [Order] fix typo in OrderItem and QuoteItem (https://github.com/coreshop/CoreShop/pull/906)
   - [NotificationBundle] fix typo in serializer namespace declaration (https://github.com/coreshop/CoreShop/pull/901)
   - [CoreBundle] fix notification rule setting for order mail action (https://github.com/coreshop/CoreShop/pull/886)
   - [Core] use PriceCalculatorInterface in product tracking extractor (https://github.com/coreshop/CoreShop/pull/892)
   - [Core] fix not passing full configuration in store based email (https://github.com/coreshop/CoreShop/pull/917)
   - [Core] fix copying object brick data from cart to sale (https://github.com/coreshop/CoreShop/pull/918)
   - [CoreBundle/OrderBundle] KernelResponse Event should ignore Profile Toolbar (https://github.com/coreshop/CoreShop/pull/919)
   - [Pimcore] Make CoreShop compatible with Pimcore 5.7.2 (https://github.com/coreshop/CoreShop/pull/914)
   
 - Features:
   - [FrontendBundle] make private method protected (https://github.com/coreshop/CoreShop/pull/890)
   - [AddressBundle] introduce address-identifiers (https://github.com/coreshop/CoreShop/issues/830 & https://github.com/coreshop/CoreShop/pull/913)

## 2.0.5:
 - Deprecations:
   - [WorkflowBundle] refactor state change logging (https://github.com/coreshop/CoreShop/pull/835)
     - `CoreShop\Bundle\OrderBundle\Workflow\OrderHistoryLogger` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface` instead
     - `CoreShop\Bundle\OrderBundle\Workflow\OrderStateHistoryLogger` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\History\StateHistoryLoggerInterface` instead
     - `CoreShop\Bundle\OrderBundle\Workflow\WorkflowStateManager` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface` instead
 - Bugs
   - [PimcoreBundle] Make embedded classes work for Pimcore 5.6 (https://github.com/coreshop/CoreShop/pull/867)
   - [All] Make CoreShop 2.0.x compatible with Pimcore 5.7 (https://github.com/coreshop/CoreShop/pull/869)
   - [All] fix PHP5 sort issues by removing them (https://github.com/coreshop/CoreShop/issues/840)
   - [Shipping] if cart has no shippables, don't force carriage calculation (https://github.com/coreshop/CoreShop/pull/863)
   - [Checkout] validate cart before submitting the order (https://github.com/coreshop/CoreShop/pull/858)
   - [Frontend] remove unused property in templates (https://github.com/coreshop/CoreShop/pull/859)
   - [Permissions] prefix permission labels (https://github.com/coreshop/CoreShop/pull/855)
   - [Order] refactor creation of invoices/shipments (https://github.com/coreshop/CoreShop/pull/852 + https://github.com/coreshop/CoreShop/pull/849)
   - [Order] introduce options for order-document-items (https://github.com/coreshop/CoreShop/pull/848)
   - [Autoloading] fix paths (https://github.com/coreshop/CoreShop/pull/846)
   - [ResourceBundle] Introduce more basic resource-types (https://github.com/coreshop/CoreShop/pull/838 + https://github.com/coreshop/CoreShop/pull/845)
   - [AddressBundle] add missing translations (https://github.com/coreshop/CoreShop/pull/836)
   
- Features
   - [Index] Introduce advanced sorting for indexes (https://github.com/coreshop/CoreShop/pull/856) 

## 2.0.4
 - Features:
   - [Reports] Reports Grid Sorting (https://github.com/coreshop/CoreShop/pull/828)
   - [Reports] Add Paginator To All Reports (https://github.com/coreshop/CoreShop/pull/826)
   - [Pimcore] introduce twig placeholder (https://github.com/coreshop/CoreShop/pull/827)
   - [Resource] add serialization for pimcore types (https://github.com/coreshop/CoreShop/pull/818)
   - [Resource] add more doctrine-pimcore-types (https://github.com/coreshop/CoreShop/pull/816)

 - Bugs:
   - [Order] fix code-generator length (https://github.com/coreshop/CoreShop/pull/833)
   - [JS] Split JS Helpers into several regions of usability (https://github.com/coreshop/CoreShop/pull/829)
   - [Core] fix for cart-item digital product never set (https://github.com/coreshop/CoreShop/pull/821)
   - [Docs] Fix dead link (https://github.com/coreshop/CoreShop/pull/822)
   - [Resource] move settings icon to resource-bundle (https://github.com/coreshop/CoreShop/pull/815)
   - [Tracker] Update CompositeTracker::trackCheckoutStep (https://github.com/coreshop/CoreShop/pull/810)

## 2.0.3
 - Features:
   - [All] Replace intval with int cast (https://github.com/coreshop/CoreShop/pull/805)
   - [Core] Store Price Dirty Detection if installed Pimcore Version >= 5.5 (https://github.com/coreshop/CoreShop/pull/807)
   - [Core] Allow Variants in Product Rule Conditions (https://github.com/coreshop/CoreShop/pull/794)
   - [Core] Add Event for Settings (https://github.com/coreshop/CoreShop/pull/785)
   - [Core] Extract CartItem Processor (https://github.com/coreshop/CoreShop/pull/784)
   - [Core] Decouple Shipping Calculator from Cart (https://github.com/coreshop/CoreShop/pull/783)
   - [Order] extract DataLoader into Pimcore Component (https://github.com/coreshop/CoreShop/pull/782)
   - [Order] rename Pimcore Grid Operators to be more CoreShop specific (https://github.com/coreshop/CoreShop/pull/787)
   - [Order] Check also for CurrencyAwareInterface in PriceFormatter Gird Operator (https://github.com/coreshop/CoreShop/pull/788) 
   - [Index] Introduce Iterator Interpreter (https://github.com/coreshop/CoreShop/pull/802)
   - [Index] Introduce new Abstract function in AbstractWorker to allow handling of array data (https://github.com/coreshop/CoreShop/pull/803)
   - [Pimcore] add object_method twig function (https://github.com/coreshop/CoreShop/pull/809)
 - Bugs:
   - [Core] Fix Gift Cart Price Rule Action (https://github.com/coreshop/CoreShop/pull/796)
   - [Core] Fix Invoice WKHTML Settings (https://github.com/coreshop/CoreShop/pull/786)
   - [Core] Rule Conditions: Check on type (https://github.com/coreshop/CoreShop/pull/779)
   - [Core] Add Translation for Adjustments (https://github.com/coreshop/CoreShop/pull/774)
   - [Pimcore] allow spaces in DynamicDropdown (https://github.com/coreshop/CoreShop/pull/781)
   
# 2.0.2
 - Pimcore:
   - This release makes CoreShop compatible with Pimcore 5.6.0 (https://github.com/coreshop/CoreShop/pull/762)
 - Features:
   - [Core] Adds a new CoreShop JS Event to add custom Menu Items to the CoreShop Menu (https://github.com/coreshop/CoreShop/pull/765)
   - [Resource] [ResourceBundle] add JMS Serializer Handler for Pimcore Objects (https://github.com/coreshop/CoreShop/pull/766)
 - Bugs:
  - [Tracking] Fixes a Bug in the Tracking Manager when a Product does not have any categories applied (https://github.com/coreshop/CoreShop/pull/767)
 
# 2.0.1
 - Features:
    - [Core] Remove login customer after successfully registration (https://github.com/coreshop/CoreShop/pull/735)
 - Bugs:
    - [Core] Countries are removed when removing Store (https://github.com/coreshop/CoreShop/pull/746)
    - [Core] order Document State Resolver when a Document is cancelled (https://github.com/coreshop/CoreShop/pull/738)
    - [Core] safe path for folders (https://github.com/coreshop/CoreShop/pull/742)
    - [Core] Fix for StoreMailActionProcessor exception in Notification Rule (https://github.com/coreshop/CoreShop/pull/740)
    - [Shipping] is invalid when no Shipping Rules are given (https://github.com/coreshop/CoreShop/pull/741)
    - [Frontend] Inaccurate Store Filter Query in Category Controller (https://github.com/coreshop/CoreShop/pull/744)

## 2.0.0
 - CoreShop\Component\Index\Condition\RendererInterface has been deprecated in favor of CoreShop\Component\Index\Condition\DynamicRendererInterface to allow dynamic registration of condition renderers

## 2.0.0-RC.1
 - Flash Messages are translated in the Controllers now, not in views anymore. If you have custom Flash Messages, translate them in your Controller instead of the view.

## 2.0.0-beta.4
 - Completely remove FOSRestBundle, you still can use it, but you need to install it yourself. CoreShop only used the BodyListener to decode POST/PUT Requests, this Listener is now added by CoreShop if FOSRestBundle is not installed.

## 2.0.0-beta.3 to 2.0.0-beta.4
 - **BC break**: All occurrences of parameters `coreshop.all.stack.pimcore_class_ids`, `"application".model."class".pimcore_class_id`, `coreshop.all.pimcore_classes.ids` have been removed. Inject the corresponding Repository and use `classId` function instead
 - **Pimcore**: CoreShop now requires at least Pimcore 5.4.0. You need to update Pimcore to the at least 5.4.0 in order to use/update CoreShop.

### Product Price Calculation
In order to allow custom price calculation on API Level, we changed the way CoreShop calculates product prices by introducing a new parameter to every PriceCalculation Interface. Price Calculator Conditions are not anymore using a Live context, instead it gets passed via API.

Following interfaces have changed:

 - ```CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableRetailPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductDiscountCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductDiscountPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface```

If you have anything customized with those classes, please change them accordingly.

If you use the PriceCalculator Service directly, you also need to change the call from

```
$this->priceCalculator->getPrice($object, true)
```

to

```
$this->priceCalculator->getPrice($object, [
    'store' => $store,
    'country' => $country,
    'customer' => $customer,
    'currency' $currency,
    'cart' => $cart
], true)
```

The new second argument, is the Context for which you want to get the Price. This is highly depends on your
need. On a default CoreShop setup, the context can be fetched form the `ShopperContext` Service like this:

```
return  [
    'store' => $this->shopperContext->getStore(),
    'customer' => $this->shopperContext->hasCustomer() ? $this->shopperContext->getCustomer() : null,
    'currency' => $this->shopperContext->getCurrency(),
    'country' => $this->shopperContext->getCountry(),
    'cart' => $this->shopperContext->getCart()
];
```

CoreShop makes that easier for you, you can just use ```$this->shoppperContext->getContext()```. But be aware, the Shopper Context is only in a Web Request available.
When you work on a CLI Level, you have to define the context yourself.

If you already have a cart and want to calculate the price for the cart, the context is a bit different, cause it resolves from the cart

```
$context = [
    'store' => $cart->getStore(),
    'customer' => $cart->getCustomer() ?: null,
    'currency' => $cart->getCurrency(),
    'country' => $cart->getStore()->getBaseCountry(),
    'cart' => $cart
];
```

### Taxation
Tax Rule Store relation has been removed as it makes currently no sense.

### Tracking
Tracking bundle has completely been refactored in order to support more use-cases than just ecommerce. If you have any customizations made, please check the current implementation to adapt your changes.

### Order Invoice
Due to changes in adjustments, we also need to change OrderInvoice/OrderInvoiceItem.

After you have migrated to the latest version you also have to remove some fields:

#### CoreShopOrderInvoice
- totalTax
- baseTotalTax
- subtotalTax
- baseSubtotalTax
- shippingTax
- baseShippingTax
- taxes
- baseTaxes
- discountTax
- baseDiscountTax
- discountNet
- discountGross
- baseDiscountNet
- baseDiscountGross
- shippingNet
- shippingGross
- baseShippingNet
- baseShippingGross
- shippingTaxRate

#### CoreShopOrderInvoiceItem
- totalTax
- baseTotalTax
- taxes
- baseTaxes

### Adjustments
> **BC break / New Feature**

There are several deprecated class fields.
After you have migrated to the latest version you also have to remove them:

#### CoreShopOrder / CoreShopQuote
- shippingGross
- shippingNet
- shippingTax
- discountGross
- discountNet
- discountTax
- baseShippingNet
- baseShippingGross
- baseShippingTax
- baseDiscountNet
- baseDiscountGross
- totalTax
- baseTotalTax
- subTotalTax
- baseSubtotalTax

#### CoreShopOrderItem / CoreShopQuoteItem
- totalTax
- baseTotalTax

#### CoreShopCart
- shippingGross
- shippingNet
- discountGross
- discountNet

## 2.0.0-beta.2 to 2.0.0-beta.3
 - **BC break** Signature of following interfaces changed:
    - `CoreShop\Component\Index\Interpreter\InterpreterInterface`: public function interpret($value, IndexableInterface $object, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface`: public function interpretForLanguage($language, $value, IndexableInterface $object, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Index\Interpreter\RelationInterpreterInterface`: public function interpretRelational($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Customer\Model\UserInterface::ROLE_DEFAULT` renamed `CoreShop\Component\Customer\Model\UserInterface::CORESHOP_ROLE_DEFAULT`
    - `CoreShop\Component\Customer\Model\UserInterface::ROLE_SUPER_ADMIN` renamed `CoreShop\Component\Customer\Model\UserInterface::CORESHOP_ROLE_SUPER_ADMIN`

 - **BC break** Shipment / Invoice Creation via API changed
    - Before adding a new Shipment / Invoice you need to dispatch a request state to your order. Read more about it [here](./docs/03_Development/06_Order/05_Invoice/01_Invoice_Creation.md) and [here](./docs/03_Development/06_Order/06_Shipment/01_Shipment_Creation.md).
 - **BC break** getName in `CoreShop\Component\Index\Model\IndexableInterface` has been changed to `getIndexableName` as `getName` could eventually conflict with a non localized Pimcore Field
 - **BC break** getEnabled in `CoreShop\Component\Index\Model\IndexableInterface` has been changed to `getIndexableEnabled` as `getEnabled` could eventually conflict with a localized Pimcore Field

## 2.0.0-beta.1 to 2.0.0-beta.2
 - Link Generator implemented. If you want to use nice urls, you need to add the link generator service:
    - CoreShopProduct: add `@coreshop.object.link_generator.product` as Link Provider
    - CoreShopCategory: add `@coreshop.object.link_generator.category` as Link Provider
    - Change `{{ path('') }}` to `{{ coreshop_path('') }}`. You may want to checkout the FrontendBundle to get a deeper insight.
 - Deprecated Field Names in - CoreShop\Component\Payment\Model\PaymentInterface`:
    - getName is now getTitle
    - getOrderId is now getOrder and directly returns a OrderInterface
 - Deprecated Field Names in - `CoreShop\Component\Shipping\Model\CarrierInterface`:
    - getLabel is not getTitle
    - getName is now getIdentifier


## 2.0.0-alpha.4 to 2.0.0-beta.1
 - **BC break** Signature of following interfaces changed:
    - `CoreShop\Component\Index\Interpreter\InterpreterInterface`: public function interpret($value, IndexableInterface $object, IndexColumnInterface $config);
    - `CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface`: public function interpretForLanguage($language, $value, IndexableInterface $object, IndexColumnInterface $config);
    - `CoreShop\Component\Index\Interpreter\RelationInterpreterInterface`: public function interpretRelational($value, IndexableInterface $indexable, IndexColumnInterface $config);

 - **BC break** CoreShop now takes advantage of the dependent bundle feature introduced in Pimcore 5.1.2. Therefore,
 all bundles are now automatically loaded. This is a BC break, as when updating, you might run into issues.
 To solve the issues, check following things:
    - remove `RegisterBundles` entry from `app/AppKernel.php`
    - remove loading CoreShopCoreBundle Configuration in `app/config.yml`
    - enable CoreShopCoreBundle via CLI or manually in `var/config/extensions.php`: `"CoreShop\\Bundle\\CoreBundle\\CoreShopCoreBundle" => TRUE`
 - **BC break** Upgraded Default Layout to Bootstrap 4. This will most certainly cause issues when you just override certain templates. Best would be to copy all templates from before the Bootstrap 4 Upgrade.

## 2.0.0-alpha.4 to 2.0.0-alpha.5
 - **BC break** added Component\Core\Model\OrderItem and Component\Core\Model\QuoteItem. If you already customized them, inherit them from the Core Models.
 - **BC break** changed the way CoreShop processes Cart-Rules. If you implemented a custom-condition, inherit from `CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker` and implement `isCartRuleValid` instead of `isValid`
- Deprecated: remove `cart/add-price-rule/` static route.

## 2.0.0-alpha.3 to 2.0.0-alpha.4
 - **BC break** decoupled MoneyBundle from CurrencyBundle, therefore the Twig Extension for Money Conversion went to the CurrencyBundle. Therefore the name of that extension was renamed from
   **coreshop_convert_money** to **coreshop_convert_currency**. If you use it directly in your code, please rename all of them.
 - Deprecated CoreShopAdminBundle which responsability was only handling installation and deliviering pimcore resources like JS and CSS files.
    * Installation has been moved to CoreShopCoreBundle
    * Delivering of resources is now handled by CoreShopResourceBundle, this also makes it easier to use CoreShop Bundles without handling resources yourself.

## 2.0.0-alpha.2 to 2.0.0-alpha.3
 - **BC break** getPrice in PurchasableInterface and ProductInterface has been removed. In favor of this a new coreShopStorePrice editable has been introduced, which stores prices for each store. This makes handling of multiple currencies way more elegant.
 
   If you still want to use the old getPrice, create a new Subclass of \CoreShop\Component\Core\Model\Product, implement \CoreShop\Component\Order\Model\PriceAwarePurchasableInterface and set your class to CoreShopProduct parents.

# V1 to V2
 - CoreShop 2 is not backward compatible. Due to the framework change, we decided to re-make CoreShop from scratch. If you still have instances running and want to migrate, there is a basic migration way which gets you data from V1 to V2.
 - [Export from CoreShop1](https://github.com/coreshop/CoreShopExport)
 - [Import into CoreShop2](https://github.com/coreshop/ImportBundle)

# Within V1
 - Nothing available
