# Price Rule Actions

Get a brief overview of all the available *Price Rule Actions* in CoreShop.

## Free Shipping

> **Available for**: Cart Price Rules

This action allows free shipping when added to a cart price rule.

## Gift Product

> **Available for**: Cart Price Rules

Add a gift to the customer's cart with this action.

#### Options

| Name         | Description                                               |
|:-------------|:----------------------------------------------------------|
| Gift Product | The product must be a registered CoreShop product object. |

## Discount Amount

> **Available for**: Cart Price Rules, Specific Product Price, Product Price

Apply a fixed amount of discount.

#### Options

| Name     | Description                                                                               |
|:---------|:------------------------------------------------------------------------------------------|
| Amount   | Define the discount amount.                                                               |
| Gross    | Check if the amount includes VAT.                                                         |
| Currency | Specify the currency for the amount.                                                      |
| Apply On | Choose between `total` and `subtotal`. **Note:** Only available in the cart rule context. |

## Discount Percent

> **Available for**: Cart Price Rules, Specific Product Price, Product Price

Set a percentage-based discount.

#### Options

| Name     | Description                                                                               |
|:---------|:------------------------------------------------------------------------------------------|
| Percent  | Define the discount percentage.                                                           |
| Apply On | Choose between `total` and `subtotal`. **Note:** Only available in the cart rule context. |

## New Price

> **Available for**: Specific Product Price, Product Price

Set a new price for a product.

> **Note**: This is used as the new sale price in the frontend, without special discount labelling. For a highlighted
> discount price, use the `Discount Price` action.

#### Options

| Name     | Description           |
|:---------|:----------------------|
| Price    | Set the new price.    |
| Currency | Specify the currency. |

## Discount Price

> **Available for**: Specific Product Price, Product Price

Define a discounted price for a product.

#### Options

| Name     | Description               |
|:---------|:--------------------------|
| Price    | Set the discounted price. |
| Currency | Specify the currency.     |
