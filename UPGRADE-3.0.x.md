# Upgrade to 3.0

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

# Shipping Price Calculation
Introduced the context parameter like we have for Product Price Calculation to determine certain context variables like
store or currency.
