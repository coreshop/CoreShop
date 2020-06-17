# Within 2.2

## 2.2.2
 - Bugs:
    - [IndexBundle] fix range filter condition (https://github.com/coreshop/CoreShop/pull/1416, https://github.com/coreshop/CoreShop/issues/1387)
    - [OrderBundle] Fix currency formatting in sale detail related components (https://github.com/coreshop/CoreShop/pull/1421)
    - [OrderBundle] Fix order expire command (https://github.com/coreshop/CoreShop/pull/1422)
    - [Product] re-add id reset on entity clone (https://github.com/coreshop/CoreShop/pull/1419)
    - [ProductBundle] fix quantity price rule condition (https://github.com/coreshop/CoreShop/pull/1412)
    - [Installer] fix output of thumbnail installer (https://github.com/coreshop/CoreShop/pull/1413)
    - [IndexBundle] fix saving of nested filters (https://github.com/coreshop/CoreShop/pull/1415, https://github.com/coreshop/CoreShop/issues/1414)
    - [CoreBundle] fix typo in query condition to fetch product variants (https://github.com/coreshop/CoreShop/pull/1418)
    - [FrontendBundle] Fixed issue with saving address changes (https://github.com/coreshop/CoreShop/pull/1408)
    - [RuleBundle] improve dirty detection (https://github.com/coreshop/CoreShop/pull/1410)
    - [CurrencyBundle] fix cache issue with money-currency type (https://github.com/coreshop/CoreShop/pull/1406)
    - [QuantityPriceRules] fix decimal precision display (https://github.com/coreshop/CoreShop/pull/1398, https://github.com/coreshop/CoreShop/issues/1395) 
 
 - Features:
    - [FrontendBundle] add italian translations (https://github.com/coreshop/CoreShop/pull/1417) big thanks to @ramundomario
    
## 2.2.1
 - Bugs:
    - [CoreBundle] fix registration service (https://github.com/coreshop/CoreShop/pull/1391)
    - [CoreBundle] fix validation groups (https://github.com/coreshop/CoreShop/pull/1390)
    - [PimcoreBundle] Dynamic Dropdowns Issues (https://github.com/coreshop/CoreShop/issues/1380, https://github.com/coreshop/CoreShop/pull/1382)
    - [FrontendBundle] revert url-forward, doesn't work for all cases  (https://github.com/coreshop/CoreShop/pull/1386, https://github.com/coreshop/CoreShop/issues/1383)
    
## 2.2.0
 - Features:
    - [Order] don't allow order-revise when completed payment has been made (https://github.com/coreshop/CoreShop/pull/1334)
    - [Order] persist internal cancellation reasons (https://github.com/coreshop/CoreShop/pull/1333)
    - [Pimcore] require min 6.6 (https://github.com/coreshop/CoreShop/pull/1338)
    - [GithubAction] add stan test (https://github.com/coreshop/CoreShop/pull/1341)
    - [OrderBundle] show price rules without tax in backend (https://github.com/coreshop/CoreShop/pull/1346)
    - [ThemeBundle] introduce theme inheritance (https://github.com/coreshop/CoreShop/pull/1353, https://github.com/coreshop/CoreShop/pull/1359)
    - [Order] introduce paymentTotal Property to store the rounded payment value with a precision of 2 (https://github.com/coreshop/CoreShop/pull/1360)
    - [Order] use Javascript intl for currency format (https://github.com/coreshop/CoreShop/pull/1366)
    - [FrontendBundle] allow preview from admin-mode and redirect to right URL if wrong (https://github.com/coreshop/CoreShop/pull/1367)
    
 - Bugs:
    - [SecurityValidator] only trigger when Pimcore Frontend request (https://github.com/coreshop/CoreShop/pull/1339)
    - [PimcoreBundle] Fix loading dynamic dropdown options (https://github.com/coreshop/CoreShop/pull/1340)
    - [PimcoreBundle] fix dynamic dropdown extensions (https://github.com/coreshop/CoreShop/pull/1337)
    - [PimcoreBundle] Fix data persistence for class definition and database (https://github.com/coreshop/CoreShop/pull/1343)
    - [PimcoreBundle] add dependency resolving (https://github.com/coreshop/CoreShop/pull/1348)
    - [CoreBundle] add missing alias for taxed product price calculator (https://github.com/coreshop/CoreShop/pull/1355)
    - [Migration] move migration before other migration in order to avoid missing db columns (https://github.com/coreshop/CoreShop/pull/1356)
    - [Payment] Revert "decouple Payment from Payum and consider decimal factor" (https://github.com/coreshop/CoreShop/pull/1358)
    - [Pimcore] fix getDataForEditmode (https://github.com/coreshop/CoreShop/pull/1361)
    - [ThemeBundle] fix pimcore bc-break (https://github.com/coreshop/CoreShop/pull/1363)
    - [Product] serialize stopPropogation property (https://github.com/coreshop/CoreShop/pull/1365)
    - [Product] fix null value for pricing (https://github.com/coreshop/CoreShop/pull/1370)
    - [ResourceBundle] ignore length for name of CoreShop doctrine assets (also fixes the error on cancel) (https://github.com/coreshop/CoreShop/pull/1369)
    - [CoreBundle] only show restore-inheritance when actually inheritable (https://github.com/coreshop/CoreShop/pull/1368)
    

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
    - [CoreBundle] fix typo in validation groups and fix guest-registration type (https://github.com/coreshop/CoreShop/pull/1304)
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
