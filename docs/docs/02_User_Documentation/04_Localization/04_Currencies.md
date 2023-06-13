# CoreShop Currencies


A Currency consists of following values:

 - Name
 - ISO Code
 - Numeric ISO Code
 - Symbol
 - Exchange Rate: Is used to convert between different currencies

If you are going to use Currency conversion, you need to update exchange ranges regularly.

![Currencies](img/currencies.png)

## Automated Exchange Rates

CoreShop supports mulitple providers to get exchange ranges from. Currently supported:

 - CentralBankOfRepulicTurkey
 - EuropeanCentralBank
 - GoogleFinance
 - NationalBankOfRomania
 - YahooFinance
 - WebserviceX

To activate this feature, enable it within CoreShop Settings and choose your provider.