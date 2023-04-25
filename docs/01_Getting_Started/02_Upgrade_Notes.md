# CoreShop Upgrade Notes

Always check this page for some important upgrade notes before updating to the latest coreshop build.

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
    - Before adding a new Shipment / Invoice you need to dispatch a request state to your order. Read more about it [here](./03_Development/06_Order/05_Invoice/01_Invoice_Creation.md) and [here](./03_Development/06_Order/06_Shipment/01_Shipment_Creation.md).
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
