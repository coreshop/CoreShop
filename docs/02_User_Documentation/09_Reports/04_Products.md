# CoreShop Products Report

![Products Report](img/products.png)

| Type | Has Pagination |
|:-----|:-----------|
| List | Yes |

## Available Filters

| Name | Description |
|:-----|:------------|
| Store | Filter by Store |
| Day | Shortcut Filter by current Day |
| Month | Shortcut Filter by current Month |
| Year | Shortcut Filter by current Year |
| Day -1 | Shortcut Filter by last Day |
| Month -1 | Shortcut Filter by last Month |
| Year -1 | Shortcut Filter by last Year |
| From | Date to Start Filter |
| To | Date to End Filter |
| Product Types | Group Filter by `Main Products`, `Variants`, `Container Products` |

## Product Types

| Name | Description |
|:-----|:------------|
| Main Products | Only show Products without Variant inclusion |
| Variants | Only show Variant Product Types |
| Container Products | Show Sum of Products and Child-Products. Note: Container Products are bounded to the original pimcore objects. The products will be ignored in this report, if those main-objects have been deleted! |

## Available Grid Fields

| Name | Description |
|:-----|:------------|
| Name | Product Name |
| Order Count | Amount of Order |
| Quantity | Quantity |
| Sale Price | Sale Price |
| Sales | Amount of Sales |
| Profit | Amount of Profit |