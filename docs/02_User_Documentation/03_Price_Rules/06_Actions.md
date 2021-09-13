# CoreShop Price Rule Actions
Get a brief overview of all the available *Price Rule Actions* in CoreShop.

## Free Shipping
> **Available for**: Cart Price Rules

Add this action to allow free shipping.

## Gift Product
> **Available for**: Cart Price Rules

Add this action to place a gift in customers cart.

#### Options

| Name | Description|
|:-----|:--------------------|
| Gift Product | Needs to be a coreshop registered product object |

## Discount Amount
> **Available for**: Cart Price Rules, Specific Product Price, Product Price

Set a arbitrary amount of discount.

#### Options

| Name | Description|
|:-----|:--------------------|
| Amount | Define Amount |
| Gross | If given Amount has included VAT, check it |
| Currency | Set Currency of given amount |
| Apply On | Select Between `total` and `subtotal`. **Note:** This option is only available in the cart rule context |

## Discount Percent
> **Available for**: Cart Price Rules, Specific Product Price, Product Price

Set a arbitrary percentage amount of discount.

#### Options

| Name | Description|
|:-----|:--------------------|
| Percent | Define Amount |
| Apply On | Select Between `total` and `subtotal`. **Note:** This option is only available in the cart rule context |


## New Price
> **Available for**: Specific Product Price, Product Price

Define a new Price.
> **Note**: This will be used as a new price in frontend so there is no "special discount price" labelling.
> If you need a highlighted discount price, use the `Discount Price` Action.

#### Options

| Name | Description|
|:-----|:--------------------|
| Price | Set new Price |
| Currency | Set Currency of given amount |

## Discount Price
> **Available for**: Specific Product Price, Product Price

Define a discount Price.

#### Options

| Name | Description|
|:-----|:--------------------|
| Price | Set discount Price |
| Currency | Set Currency of given amount |