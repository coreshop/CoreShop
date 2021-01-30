CoreShop's Settings are divided into each "sub"-shop and some system settings.

### Shop-Settings

Settings can be different for each Store.

![Settings](img/settings-shop.png)

#### Base

- Guest-Checkout: Enables or disables guest-checkout feature

#### Customer Service
 - Customer Email Document: Email document used to send customer messages
 - Customer Re Email Document: Email document used to send customer reply messages
 - Contact Email Document: Email document used to send contact messages
 - Contact Sales: Default contact used for sales messages
 - Contact Technology: Default contact used for technology messages

#### Stock
 - Default Stock Behaviour: Describes the default stock behaviour for products

#### Tax
 - Validate VAT: Describes if the VAT-Number for European Countries should be validated
 - Disable VAT for Base Country: Disable VAT-Free Shopping for Customers, with valid Vat-Number, in Base Country
 - Taxation Address: Describes witch address is responsibly for taxation

#### Prices
 - Prices are gross prices: Determines if entered prices in CoreShop are with or without tax included.

#### Shipping
 - Free Shipping starts at weight: Describes free shipping at weight. It's also much faster using this than price-rules
 - Free Shipping starts at Currency: Describes free shipping at cart value. It's also much faster using this than price-rules
 - Carrier Sort: Describes how the Carriers should be sorted

#### Product
 - Default Image: Defines an default image for products if no image is available.
 - Number of days as new: Defines the time of days a product is marked as new.

#### Category
 - Default Image: Defines an default image for categories if no image is available.

#### Invoice
 - Create Invoice for Orders: Defines if invoices are going to be created on an paid order.
 - Prefix: Prefix string for Order and Invoice numbers
 - Suffix: Suffix string for Orders and Invoice numbers.
 - WKHTMLTOPDF Parameters: Parameters which will be passed to WKHTMLTOPDF

#### Mail Settings
 - admin email-addresses to send order notification (CSV): Email addresses which will be notified on new orders
 - Send OrderStates as BCC to admin email-addresses: Determines if order-state emails should also be sent to admin-emails

#### Cart
 - Activate automatic cart cleanup: Activate automatic cart cleanup -> cleans inactive and empty carts

### System - Settings

System Settings are defined one time for all shops.

![System Settings](img/settings-system.png)

#### System Settings
 - Send Usagelog to CoreShop: Sends an anonymous usagelog to CoreShop Developer

#### Currency
 - Automatic Exchange Rates: Describes if exchange rates should be fetched automatically
 - Exchange Rate Provider: Describes which exchange rate provider should be used.
