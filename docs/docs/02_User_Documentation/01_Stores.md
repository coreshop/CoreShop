# CoreShop Stores
Every CoreShop Installation is bounded to a default Store.
By default a store is connected with a pimcore Site.

![Stores](img/stores.png)

## Domain based Store
This is CoreShops default behaviour. One store for your default page, which is also your default store.
If you have multiple Domains it's possible to add a store to each domain.

> **Tip:** Learn more about pimcore multi sites: [Pimcore Sites](https://pimcore.com/docs/pimcore/current/Development_Documentation/MVC/Routing_and_URLs/Working_with_Sites.html)

## Custom Store Locator
Sometimes you have just one site installation but you want do use different Stores.
Example: `/de-at` should use a austrian store and `/de-de` a german one.
For that you need to build a custom store locator to do so, checkout the dev section to find out how this can be achieved.

## Configure CoreShop Stores
Open CoreShop Menu -> "Stores". There should be already a default store, connected to your default (main-domain) site.
To create a new store, click on the "Add" button. Proceed wih configuring your new store.

## Examples
Stores can be used in many ways, checkout the examples to find out which solution suits you most.

### Example 1: Global Store
Use a Store to handle multiple currencies, countries and tax calculations.
Depending on your product and price strategy, it's possible to use it within one store.

### Example 2: Country Store
Use a Store for each Country. This allows you to restrict your shop within a country context.