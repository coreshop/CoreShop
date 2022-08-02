# CoreShop Price Rules Conditions
Get a brief overview of all the available *Price Rule Conditions* in CoreShop.

## Customers
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Define one ore more customers to whom the price rule should get applied.

#### Options

| Name | Description|
|:-----|:--------------------|
| Customers | One ore multiple CoreShop Customer Objects |

## Customer Groups
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Define one ore more customer groups to whom the price rule should get applied.

#### Options

| Name | Description|
|:-----|:--------------------|
| Groups | One ore multiple CoreShop Customer Group Objects |

## Time Span
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Define a time span in which range the price rule is valid

#### Options

| Name | Description|
|:-----|:--------------------|
| Date From | Date/Time Start |
| Date To | Date/Time End |

## Voucher
> **Available for**: Cart Price Rules

Define some Voucher Conditions. Check out the [Voucher Section](./05_Vouchers.md) to learn more about Voucher Configuration.

#### Options

| Name | Description|
|:-----|:--------------------|
| Max. Usage per Code | Define how often a voucher code can be used. |
| Allow only one Voucher per Code | If checked, only one Voucher per Cart is allowed. |

## Amount
> **Available for**: Cart Price Rules

Define a Price range within the price rule should get applied.

#### Options

| Name | Description|
|:-----|:--------------------|
| Min Amount | The min amount of cart |
| Max Amount | The max amount of cart |

## Countries
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Bind a specific country to the price rule.
**Important**: This Condition is connected with the `Country Context Resolver`.
This Condition does not implies to use the Users current location since the Resolver could be also store related.

#### Options

| Name | Description|
|:-----|:--------------------|
| Countries | Select one or multiple Countries |

## Zones
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Bind a specific zone to the price rule.
**Important**: This Condition is connected with the `Country Context Resolver`.
This Condition does not implies to use the Users current location since the Resolver could be also store related.

#### Options

| Name | Description|
|:-----|:--------------------|
| Zones | Select one or multiple Zones |

## Stores
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Bind a specific store to the price rule.

#### Options

| Name | Description|
|:-----|:--------------------|
| Stores | Select one or multiple Stores |

## Currencies
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Define which currencies are valid to apply the price rule

#### Options

| Name | Description|
|:-----|:--------------------|
| Currencies | Select one or multiple Currencies |

## Carriers
> **Available for**: Cart Price Rules

Define which carriers are valid to apply the price rule

#### Options

| Name | Description|
|:-----|:--------------------|
| Carriers | Select one or multiple Carriers |

## Nested Rules
> **Available for**: Cart Price Rules, Specific Product Prices, Product Price

Build complex `AND`, `OR` and `NOT` nested conditions.
Within a nested rules it's possible to add any other condition again.

#### Options

| Name | Description|
|:-----|:--------------------|
| Operator | `AND`, `OR` and `NOT` |

## Products
> **Available for**: Cart Price Rules, Product Price

Apply rule only if given products are available.

#### Options

| Name | Description|
|:-----|:--------------------|
| Products | One ore multiple CoreShop Product Objects |

## Categories
> **Available for**: Cart Price Rules, Product Price

Apply rule only if products linked with given categories are available.

#### Options

| Name | Description|
|:-----|:--------------------|
| Categories | One ore multiple CoreShop Category Objects |
