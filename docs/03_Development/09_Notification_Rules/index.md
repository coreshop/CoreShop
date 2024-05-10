# Notification Rules

In CoreShop, Notification Rules are pivotal for managing various types of notifications triggered within the system.
These rules cater to a range of scenarios, ensuring that the right notifications are dispatched under specific
conditions.

## Overview of Notification Types

CoreShop handles notifications for the following types:

- **Order**
- **Quote**
- **Invoice**
- **Shipment**
- **User**
- **Payment**

Let's explore each notification type and their respective conditions and actions:

### Order Notifications

#### Allowed Conditions

| Name                | Description                                                                        |
|---------------------|------------------------------------------------------------------------------------|
| Invoice State       | Triggered if a specific Invoice State is active.                                   |
| Invoice Transition  | Triggered if a specific Invoice Transition has been applied.                       |
| Payment State       | Triggered if a specific Payment State is active.                                   |
| Payment Transition  | Triggered if a specific Payment Transition has been applied.                       |
| Shipping State      | Triggered if a specific Shipping State is active.                                  |
| Shipping Transition | Triggered if a specific Shipping Transition has been applied.                      |
| Order State         | Triggered if a specific Order State is active.                                     |
| Order Transition    | Triggered if a specific Order Transition has been applied.                         |
| Carriers            | Triggered if a specific Carrier has been selected in the Order.                    |
| Comment             | Triggered if a Comment Action has been applied. Available Types: `create comment`. |

#### Allowed Actions

| Name        | Description                                      |
|-------------|--------------------------------------------------|
| Order Email | Email notification with Order Object.            |
| Email       | General Email notification without Order Object. |

#### Available Placeholder Keys for Email Templates

| Key         | Value                                     |
|-------------|-------------------------------------------|
| object      | Object of type `OrderInterface`.          |
| fromState   | State identifier transitioning away from. |
| toState     | State identifier transitioning to.        |
| transition  | Used transition.                          |
| _locale     | Used locale.                              |
| recipient   | Customer Email Address.                   |
| firstname   | Customer Firstname.                       |
| lastname    | Customer Lastname.                        |
| orderNumber | Order Number.                             |

### (And similar sections for Shipment, Invoice, Payment, User, and Quote)

## Custom Implementation

For bespoke notification scenarios, custom implementation of notification rules is possible. Learn more about this
in [Custom Notification Types](./03_Custom_Types.md).

## Extending CoreShop Notification Rules

CoreShop also allows for the extension and customization of notification rules:

- **[Custom Actions](./01_Custom_Actions.md)**: Learn how to create custom actions for your notifications.
- **[Custom Conditions](./02_Custom_Conditions.md)**: Discover how to implement custom conditions for more tailored
  notifications.
- **[Custom Types](./03_Custom_Types.md)**: Explore how to set up custom notification types for specialized scenarios.
- **[Triggering Notifications](./04_Triggering.md)**: Understand the mechanisms behind triggering notifications in
  CoreShop.
