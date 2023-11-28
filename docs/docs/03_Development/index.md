# Development

This guide is intended for developers who aim to harness the flexibility and robust features of CoreShop. It outlines
the key concepts and components used in CoreShop, providing insights into its architecture and how to effectively work
with it.

## Introduction

CoreShop offers a rich set of tools and features for building sophisticated eCommerce solutions. Understanding the
underlying concepts and models is crucial for leveraging its full potential.

## Resources

CoreShop utilizes Doctrine ORM for its custom resources, offering great flexibility and extendability for managing
simple models such as currencies, countries, etc. Here are the primary resources implemented as ORM models in CoreShop:

- **Currency**: Handling various currencies.
- **Country and State**: Geographical models for location-based functionality.
- **Tax Rate and Tax Rules**: For applying taxes in different scenarios.
- **Price Rules**: Rules that define pricing under various conditions.
- **Carrier**: Shipping carriers and their configurations.
- **Shipping Rules**: Rules for handling shipping logic and pricing.
- **Index and Filter Configuration**: For product indexing and filtering.
- **Notification Rule Configuration**: Setting up rules for notifications.
- **Stores**: Representing different stores or sales channels.
- **Payments**: Handling payment methods and transactions.

In CoreShop, the term 'Resource' refers to these ORM Models.

## Pimcore Models

Leveraging Pimcore's flexible data model, CoreShop uses Data Objects for frequently used and modified entities. The
following are the key objects implemented using Pimcore Data Objects:

- **Product**: The core entity in any eCommerce platform.
- **Product Category**: Organizing products into categories.
- **Manufacturer**: Information about product manufacturers.
- **Order**: Managing customer cart/orders.
- **Order Invoice**: Generating and handling invoices.
- **Order Shipment**: Managing the shipment of orders.
- **Quote**: Handling quotes for customers.
- **Customer**: Customer data and profiles.
- **Customer Group**: Grouping customers for specific functionalities or offers.
- **Addresses**: Managing customer addresses.

When discussing 'Objects' in the context of CoreShop, it refers to these Pimcore Data Objects.
