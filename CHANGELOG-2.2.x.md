# Within 2.2

## 2.2.0-RC.2
 - Features:
    - [IndexBundle] allow configuring if versions should be indexed or not (https://github.com/coreshop/CoreShop/pull/1303)
    - [IndexBundle] add possibility to store extra information into relational table (https://github.com/coreshop/CoreShop/pull/1306)
    - [Pimcore] Compatibility with Pimcore 6.5.3 (https://github.com/coreshop/CoreShop/pull/1310)
    - [Shipping] Calculate shipping tax using cart items (https://github.com/coreshop/CoreShop/pull/1283)
    - [Doctrine] remove usage of deprecated merge (https://github.com/coreshop/CoreShop/pull/1314)
    - [Docs] Add Documentation for Unit Definitions (https://github.com/coreshop/CoreShop/pull/1312)
    - [Payment] decouple Payment from Payum and consider decimal factor (https://github.com/coreshop/CoreShop/pull/1021)
    - [Installer] change installer colors (https://github.com/coreshop/CoreShop/pull/1325)
    - [Github Actions] introduce testing with Github Actions (https://github.com/coreshop/CoreShop/pull/1329)
    
 - Bugs:
    - [AddressBundle] introduce filter-active action and filter store-base countries by those (https://github.com/coreshop/CoreShop/pull/1302)
    - [ThemeBundle] fix configuration for default resolvers (https://github.com/coreshop/CoreShop/pull/1301)
    - [IndexBundle] index ui improvements (https://github.com/coreshop/CoreShop/pull/1300)
    - [CoreBundle] fix typo in validation groups and fix guest-registration type (https://github.com/coreshop/CoreShop/pull/1304)
    - [Translations] fix: add missing translation (https://github.com/coreshop/CoreShop/pull/1308)
    - [Frontend] Fixes the category items-per-page dropdown in the frontend (https://github.com/coreshop/CoreShop/pull/1313)
    - [Customer] fix missing username field (https://github.com/coreshop/CoreShop/pull/1315)
    - [FrontendBundle/CoreBundle] prevent _fragment calls by using ACL's (https://github.com/coreshop/CoreShop/pull/1309)
    - [Reports] fix export params (https://github.com/coreshop/CoreShop/pull/1328)

## 2.2.0
 - Features:
    - [Core] Implement Username/Email Login Identifier @solverat (https://github.com/coreshop/CoreShop/issues/1290, https://github.com/coreshop/CoreShop/pull/1291)
    - [Pimcore] require min Pimcore 6.5 (https://github.com/coreshop/CoreShop/pull/1286)
    - [Core] Company - Customer Workflow @solverat (https://github.com/coreshop/CoreShop/issues/1266, https://github.com/coreshop/CoreShop/pull/1284)
    - [Scrutinizer] remove unused-code (https://github.com/coreshop/CoreShop/pull/1226)
    - [STAN] fixes (https://github.com/coreshop/CoreShop/pull/1239)
    - [PHPStan] level-3 (https://github.com/coreshop/CoreShop/pull/1220)
    - [Pimcore] remove pimcore bc layers (https://github.com/coreshop/CoreShop/pull/1221)
    - [CoreBundle] support version marshall und unmarshall to merge with existing data (https://github.com/coreshop/CoreShop/pull/1145)
 - Bugs:
    - [CoreExtensions] fix issue with CoreShop CoreExtensions Recycle Bin (https://github.com/coreshop/CoreShop/pull/1254)
