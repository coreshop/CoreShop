# CoreShop Carriers Documentation

CoreShop Carriers are an essential part of the shipping process in the CoreShop e-commerce framework, built on the Pimcore platform. Carriers represent shipping providers (e.g., UPS, FedEx, or DHL) and are responsible for delivering orders to customers. This documentation will guide you through the process of creating and managing carriers in CoreShop.

## Creating a Carrier

To create a carrier, follow these steps:

 - Log in to the CoreShop admin interface.
 - Navigate to the "Shipping" tab and click on "Carriers."
 - Click the "Add new" button to create a new carrier.
 - Enter a name for the carrier and configure the other available options as needed.

## Carrier Options

When creating a carrier, you can configure various options to customize its behavior:

 - Name: A descriptive name for the carrier (e.g., UPS, FedEx, or DHL).
 - Tracking Url: A string value that indicates the tracking url
 - Tax Rule Group: The tax rule group applied to shipping costs for this carrier. If you want to apply taxes to shipping costs, you'll need to create a tax rule group and assign it to the carrier.
 - Shipping Rules: The shipping rules associated with this carrier. Shipping rules define shipping costs based on various conditions such as weight, price, dimensions, and destination. You can assign multiple shipping rules to a carrier, and the system will evaluate them in order of priority to determine the final shipping cost.

## Summary
CoreShop Carriers provide a robust way to manage shipping providers and their associated services. By creating carriers and configuring their options, you can offer customers a range of shipping choices based on factors such as delivery time and cost. Combined with shipping rules, carriers give you the flexibility to create a tailored shipping experience for your e-commerce store.