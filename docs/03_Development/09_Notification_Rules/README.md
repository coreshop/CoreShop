# CoreShop Notification Rules

Notification Rules are responsible for all types of notification triggered by CoreShop.
It handles notification for following types:

 - order
 - quote
 - invoice
 - shipment
 - user
 - payment

## Overview
Let's checkout each notification type:

### Order

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| Invoice State | Dispatch if given Invoice State is active |
| Invoice Transition | Dispatch if given Invoice Transition has been applied |
| Payment State | Dispatch if given Payment State is active |
| Payment Transition | Dispatch if given Payment Transition has been applied |
| Shipping State | Dispatch if given Shipping State is active |
| Shipping Transition | Dispatch if given Shipping Transition has been applied |
| Order State | Dispatch if given Order State is active |
| Order Transition | Dispatch if given Order Transition has been applied |
| Carriers | Dispatch if given Carrier has been selected in Order |
| Comment | Dispatch if a Comment Action has been applied. Available Types: `create comment`  |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Order Email | Email with Order Object |
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type OrderInterface |
| fromState | State identifier from which it is transitioning away from |
| toState | State identifier from which it is transitioning to |
| transition | Used transition |
| _locale | Used locale |
| recipient | Customer E-Mail Address |
| firstname | Customer Firstname |
| lastname | Customer Lastname |
| orderNumber | Order Number |

#### Available Placeholders keys for email templates for comment

| Key  | Value |
|:-----|:------------|
| object | Object of type OrderInterface |
| _locale | Used locale |
| recipient | Customer E-Mail Address |
| firstname | Customer Firstname |
| lastname | Customer Lastname |
| orderNumber | Order Number |
| type | Type of comment |
| submitAsEmail | should comment be sent as mail |
| comment | contents of the comment |


### Shipment

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| Shipping State | Dispatch if given Shipping State is active |
| Shipping Transition | Dispatch if given Shipping Transition has been applied |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Order Email | Email with Order Object |
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type OrderShipmentInterface |
| order | Object of type OrderInterface |
| fromState | State identifier from which it is transitioning away from |
| toState | State identifier from which it is transitioning to |
| transition | Used transition |

### Invoice

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| Invoice State | Dispatch if given Invoice State is active |
| Invoice Transition | Dispatch if given Invoice Transition has been applied |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Order Email | Email with Order Object |
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type OrderInvoiceInterface |
| order | Object of type OrderInterface |
| fromState | State identifier from which it is transitioning away from |
| toState | State identifier from which it is transitioning to |
| transition | Used transition |

### Payment

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| Payment State | Dispatch if given Payment State is active |
| Payment Transition | Dispatch if given Payment Transition has been applied |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Order Email | Email with Order Object |
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type PaymentInterface |
| order | Object of type OrderInterface |
| paymentState | State of the Payment |

### User

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| User Type| Dispatch if given Type has been applied. Allowed Types: `new account`, `password reset` |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type CustomerInterface |
| recipient | Customer E-Mail Address |
| gender | Customer Gender |
| firstname | Customer Firstname |
| lastname | Customer Lastname |
| email | Customer E-Mail |
| type | type of customer notification |

#### Additional Placeholders keys for Password Reset

| Key  | Value |
|:-----|:------------|
| resetLink | Link where customer can reset his Password |

#### Additional Placeholders keys for Request Newsletter

| Key  | Value |
|:-----|:------------|
| confirmLink | Link where customer can confirm his Newsletter subscription |
| token | Confirmation Token |

### Quote

#### Allowed Conditions

| Name | Description |
|:-----|:------------|
| Carriers | Dispatch if given Carrier has been selected in Order |

#### Allowed Actions

| Name | Description |
|:-----|:------------|
| Email | Default Email without Order Object |

#### Available Placeholders keys for email templates

| Key  | Value |
|:-----|:------------|
| object | Object of type QuoteInterface |

## Custom Implementation

It's also easy to implement custom notification rules. Read more about this [here](./03_Custom_Types.md)

## Extend CoreShop Notification Rules

 - [Custom Actions](./01_Custom_Actions.md)
 - [Custom Conditions](./02_Custom_Conditions.md)
 - [Custom Types](./03_Custom_Types.md)
 - [Triggering Notifications](./04_Triggering.md)