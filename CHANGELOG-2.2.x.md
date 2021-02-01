# Within 2.2

## 2.2.9
 - Bugs:
     - [ResourceBundle] fix compatibility with Doctrine EventSubscriber (https://github.com/coreshop/CoreShop/pull/1580)
     - [All] replace all usages of href with manyToOneRelation and multihref with manyToManyRelation (https://github.com/coreshop/CoreShop/pull/1576)
     - [OptimisticEntityLockBundle] fix version for Pimcore Extensions UI (https://github.com/coreshop/CoreShop/pull/1577)
     - [Models] strict defining of model trait methods (https://github.com/coreshop/CoreShop/pull/1578)
     - [ProductBundle] use full unit definition label in choice field (https://github.com/coreshop/CoreShop/pull/1569)
     - [ResourceBundle] check if instance is null before calling class_implements (https://github.com/coreshop/CoreShop/pull/1566)
     - [Admin] use form.Panel instead of form.FieldSet (panel supports isDirty) (https://github.com/coreshop/CoreShop/pull/1561)
     - [Resource] Provide Pimcore driver for Stack Repository (https://github.com/coreshop/CoreShop/pull/1567)
     - [StoreBundle] fix cached store context decoration (https://github.com/coreshop/CoreShop/pull/1565)
     - [Mailer] remove usages of PimcoreBundle\MailerInterface and fix interface deprecation (https://github.com/coreshop/CoreShop/pull/1568)
   
## 2.2.8
 - Bugs:
     - [OptimisticEntityLockBundle] fix Version loading with Composer v3 (https://github.com/coreshop/CoreShop/pull/1558)

## 2.2.7
 - Features:
     - [OptimisticLock] allow to optimistically lock Pimcore DataObjects (https://github.com/coreshop/CoreShop/pull/1537)
     - [Pimcore] introduce DataObjectBatchListing (https://github.com/coreshop/CoreShop/pull/1519)

 - Bugs:
     - [Payment] Concurrency issues (https://github.com/coreshop/CoreShop/issues/1536, https://github.com/coreshop/CoreShop/pull/1549)
     - [Frontend] fix addressAccessType check and improve view (https://github.com/coreshop/CoreShop/pull/1544)
     - [Payment] Payment Details can be null (https://github.com/coreshop/CoreShop/issues/1545)
     - [ProductBundle] fix UnitDefinition without id (https://github.com/coreshop/CoreShop/pull/1547)
     - [Order] fix payment provider (https://github.com/coreshop/CoreShop/pull/1548)
     - [Payment] ignore failed payments in total amount check (https://github.com/coreshop/CoreShop/pull/1543)
     - [Checkout] Fix checkout with addressAccessType COMPANY_ONLY (https://github.com/coreshop/CoreShop/pull/1526)
     - [CompilerPass] rework compiler passes (simplify) (https://github.com/coreshop/CoreShop/pull/1535)
     - [Admin Order detail] Carrier name not shown with legacy serialization of Orders (https://github.com/coreshop/CoreShop/issues/1540)
     - [composer2] make getVersion compatible (https://github.com/coreshop/CoreShop/pull/1539)
     - [DynmicDropdown] support class override and fix order by id (https://github.com/coreshop/CoreShop/pull/1538)
     - [DeepCopy] Order details are no longer working (https://github.com/coreshop/CoreShop/issues/1507, https://github.com/coreshop/CoreShop/pull/1534)
     - [OrderBundle] fix voucher code export (https://github.com/coreshop/CoreShop/pull/1530)
     - [Resources] Reset Id on __clone (https://github.com/coreshop/CoreShop/issues/1501, https://github.com/coreshop/CoreShop/pull/1502)
     - [Document] Document saving failed (https://github.com/coreshop/CoreShop/issues/1498, https://github.com/coreshop/CoreShop/pull/1518)
     - [Admin] fix payment details in backend order view (https://github.com/coreshop/CoreShop/pull/1525)
     - [Notification] fix return of store on notification rule (https://github.com/coreshop/CoreShop/pull/1520)
     - [Locale] Fix PSR-4 namespace (https://github.com/coreshop/CoreShop/pull/1509)
   
  - Docs:
     - [Docs] add docu for product units (https://github.com/coreshop/CoreShop/pull/1551)
   
 - Tests:
     - [Actions] add tests for packages (https://github.com/coreshop/CoreShop/pull/1542)

## 2.2.6
 - Bugs:
     - [Product] fix cloning of ProductUnitDefinitions and add test for it. (https://github.com/coreshop/CoreShop/pull/1502)
     - [CoreBundle] interactive login: cart might not be available for several reasons, ignore exception and don't assign a cart (https://github.com/coreshop/CoreShop/pull/1500)
     
## 2.2.5
 - Bugs:
     - [OrderBundle] fix permission keys for order-creation (https://github.com/coreshop/CoreShop/pull/1474)
     - [ProductBundle] fix persistance of spefiic-product-price-rule label (https://github.com/coreshop/CoreShop/pull/1472)
     - [OrderBundle] remove 'applyOn' from DiscountPercent and SurchagePercent (https://github.com/coreshop/CoreShop/pull/1479)
     - [QPR] Wrong Pseudo-Price in Grid-View (https://github.com/coreshop/CoreShop/issues/1488)
     - [Pimcore] fix hasDefinition with pimcore.implementation_loader.document.tag (https://github.com/coreshop/CoreShop/pull/1490)
     - [OrderBundle, LocaleBundle] fix backend order-creation localeCode selection (https://github.com/coreshop/CoreShop/pull/1481)
     - [CoreBundle] fix setting price-values for inherited store-values (https://github.com/coreshop/CoreShop/pull/1491)
     - [CoreBundle] don't set product for store-values on pre-get-data (https://github.com/coreshop/CoreShop/pull/1492)
     
 - Features:
     - [Core] pass cart-item to price calculation context (https://github.com/coreshop/CoreShop/pull/1482)
     - [Index] allow to define column config with index-extensions (https://github.com/coreshop/CoreShop/pull/1494)
 
## 2.2.4
 - Bugs:
    - [CoreBundle] Don't validate maximum or minimum order quantity when value is 0 (https://github.com/coreshop/CoreShop/issues/1467, https://github.com/coreshop/CoreShop/pull/1468)
    - [Specific Price Rule] Unable to edit saved rule (https://github.com/coreshop/CoreShop/issues/1437, https://github.com/coreshop/CoreShop/pull/1452)
    - [Pimcore] Fix Ext Item Selector (https://github.com/coreshop/CoreShop/pull/1465)
    - [Core, Order] fix surchage amount processor and remove apply-on (https://github.com/coreshop/CoreShop/pull/1462)
    - [All] fix for copying data-objects with complex doctrine entities (https://github.com/coreshop/CoreShop/pull/1404)
    - [CoreBundle] fix installation of address format (https://github.com/coreshop/CoreShop/pull/1455, https://github.com/coreshop/CoreShop/issues/1432)
    - [Orders] Unable to edit grid options to add/remove fields (https://github.com/coreshop/CoreShop/issues/1438, https://github.com/coreshop/CoreShop/pull/1454)
    - [Voucher] Voucher code generator returns always the same code (https://github.com/coreshop/CoreShop/issues/1448, https://github.com/coreshop/CoreShop/pull/1451)
    - [ProductBundle] fix serialization of translation labels in product-price rules (https://github.com/coreshop/CoreShop/pull/1447)
    
 - Features:
    - [OrderBundle] check if generation of a certain amount of codes is possible before actually generating them (https://github.com/coreshop/CoreShop/pull/1456, https://github.com/coreshop/CoreShop/issues/1453)
      
## 2.2.3
 - Bugs:
    - [FrontendBundle] apply confirm and pay transition for orders with value of 0 (https://github.com/coreshop/CoreShop/pull/1442)
    - [Core] create default address if customer doesn't have one (https://github.com/coreshop/CoreShop/pull/1444)
    - [OrderBundle] Values should be zero, if amount should not be defined (https://github.com/coreshop/CoreShop/pull/1443)
    - [OrderBundle] Voucher Credit and Tax rounding issue (https://github.com/coreshop/CoreShop/pull/1441)
    - [OrderBundle] Add currency property (https://github.com/coreshop/CoreShop/pull/1436)
    - [Docs] Fix typo in the docs (https://github.com/coreshop/CoreShop/pull/1426)
    - [CoreBundle] assert default address type (https://github.com/coreshop/CoreShop/pull/1440, https://github.com/coreshop/CoreShop/issues/1257)
    
 - Features:
    - [OrderBundle] Voucher Credit and Tax rounding issue (https://github.com/coreshop/CoreShop/pull/1441)
    
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
