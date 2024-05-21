# Stores

In CoreShop, each installation is linked to a default store, typically associated with a Pimcore site. Understanding how
stores function is crucial for managing different aspects of your e-commerce platform.

![Stores](img/stores.png)

## Domain-Based Store Setup

CoreShop's default setup involves one store per domain. This means your main website domain corresponds to your default
store. If your platform operates across multiple domains, you can assign a separate store to each.

> **Tip:** For more information on managing multiple sites with Pimcore,
> see [Pimcore Sites](https://pimcore.com/docs/platform/Pimcore/MVC/Routing_and_URLs/Working_with_Sites/).

## Custom Store Locator

In scenarios where you have a single site but require different stores (for example, `/de-at` for an Austrian store
and `/de-de` for a German store), you'll need a custom store locator. Detailed guidance on creating a custom store
locator is available in the development section.

## Setting Up CoreShop Stores

To configure stores in CoreShop:

1. Navigate to CoreShop Menu -> "Stores".
2. You'll see the default store linked to your main domain.
3. To add a new store, click on the "Add" button and proceed with the setup.

## Utilizing Stores Effectively

Stores in CoreShop can be leveraged in various ways depending on your business needs:

### Global Store

- **Use Case**: Managing multiple currencies, countries, and tax calculations within a single store.
- **Advantage**: Suitable for a unified product and price strategy across different regions.

### Country Store

- **Use Case**: Setting up a store for each country, allowing for country-specific restrictions and customizations.
- **Advantage**: Ideal for businesses targeting specific national markets.

By understanding and utilizing the store configurations in CoreShop, you can tailor your e-commerce experience to meet
diverse regional requirements and customer preferences.
