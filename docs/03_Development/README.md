# CoreShop

The Developer’s guide to leveraging the flexibility of CoreShop. Here you will find all the concepts used in CoreShop.

## Introduction

## CoreShop Resources

CoreShop uses Doctrine ORM for Custom Resources. ORM enables us great flexibility and extendability for simple models like Currencies and Countries.
CoreShop currently implements these Resources as ORM Model:

 - Currency
 - Country and State
 - Tax Rate and Tax Rules
 - Price Rules
 - Carrier
 - Shipping Rules
 - Index and Filter Configuration
 - Notification Rule Configuration
 - Stores
 - Payments

Everytime something is called a Resource, we talk about ORM Models.

## CoreShop Pimcore Models

CoreShop also takes advantage of Pimcores flexible data-model.
Objects that are heavily used and changed are implemented using Data Objects:

 - Product
 - Product Category
 - Manufacturer
 - Cart
 - Order
 - Order Invoice
 - Order Shipment
 - Quote
 - Customer
 - Customer Group
 - Addresses

Everytime we talk about Objects, we talk about Pimcore Data Objects.
