# Upgrade to 3.0

## Type Hints
CoreShop now completely implemented PHP Type Hints where applicable. This is one of the main reason a simple upgrade
will not work. You have to check all your extension points for compliance to interface declarations.  

## Cart and Quote are now Order
The Cart and Quote Class are now the Order Class as well. Since these entities are similar, it makes sense to just use one.

### Migration
CoreShop will not provide any migration Script to convert carts/quotes into Orders. That is impossible for us to provide.
The Order is basically the same as before, so you can create custom migrations to move them.

Only thing we migrate is for existing orders eg. add new Fields or set the initial state.

### Differential between Cart, Quote and Order
To differentiate between them, we introduced a new Workflow and State Field called "saleType". The "saleType" currently
supports 3 types: "Cart", "Quote" and "Order". Workflow Transitions are used to move the Object to a different folder
or otherwise further process data within the Entity. The standard Workflow looks like this:


```yaml
core_shop_workflow:
    state_machine:
        coreshop_order_sales_type:
            places:
                - cart
                - order
                - quote
            transitions:
                order:
                    from: [cart]
                    to: order
                quote:
                    from: [cart]
                    to:   quote
                cart:
                    from: [cart]
                    to:   cart
            place_colors:
                cart: '#61c2cb'
                order: '#feb624'
                quote: '#f2583e'
            transition_colors:
                order: '#feb624'
                quote: '#f2583e'
            callbacks:
                after:
                    add_to_history:
                        priority: 10
                        on: ['order', 'quote']
                        do: ['@CoreShop\Bundle\WorkflowBundle\History\StateHistoryLoggerInterface', 'log']
                        args: ['object', 'event']
```

Per default it is also not allowed to change a committed Order back to a Cart. But we will provide a way to cancel Orders
and re-create a Cart out of it.

## Order Changes with Base Values
CoreShop stores the total, subtotal, etc. Values twice per Order. Once in the Customer choosen Currency, and once in the 
Store's base currency. Those values used to be called "baseValues". With 3.0 we switched that around, so the "normal" numbers
are the Store' based currency values and the Customer's choosen Currency are called "converted" Values and are accessible in
getters like "getConvertedTotal", "getConvertedItemDiscountPrice", etc.

For the migration from 2.x to 3.x, we take care about storing the values in the right field.

# Shipping Price Calculation
Introduced the context parameter like we have for Product Price Calculation to determine certain context variables like
store or currency.

## Price Twig Helper changed signature 

from

```
product|coreshop_product_price(with_tax, coreshop.context)
product|coreshop_product_retail_price(with_tax, coreshop.context)
product|coreshop_product_discount(with_tax, coreshop.context)
product|coreshop_product_discount_price(with_tax, coreshop.context)
```

to

```
product|coreshop_product_price(coreshop.context, with_tax)
product|coreshop_product_retail_price(coreshop.context, with_tax)
product|coreshop_product_discount(coreshop.context, with_tax)
product|coreshop_product_discount_price(coreshop.context, with_tax)
```

## PHP Template Engine Helpers removed
The PHP Engine has been deprecated by Symfony. Since that, we don't have plans to further support it as well.

## CoreShop\Component\Index\Model\IndexableInterface Signature changed

From

```php
    public function getIndexable();
    public function getIndexableEnabled();
    public function getIndexableName($language);
```

To

```php
    public function getIndexable(IndexInterface $index): bool
    public function getIndexableEnabled(IndexInterface $index): bool
    public function getIndexableName(IndexInterface $index, string $language): string
```

