# CoreShop Upgrade Notes

Always check this page for some important upgrade notes before updating to the latest coreshop build.

## Pimcore 10.6
If you update to Pimcore 10.6 and you get following error: ```"You have requested a non-existent parameter "coreshop.model.user.class"``` then check if you have this config in your `security.yaml`:

```yaml
pimcore:
  security:
    encoder_factories:
      ‘%coreshop.model.user.class%’: coreshop.security.user.password_encoder_factory
```

If you have that, remove that entry. CoreShop internally sets that anyway.

## 3.0.x
### 3.0.4

#### Bugs
- [PimcoreBundle] fix SluggableLinkGenerator DI Config (https://github.com/coreshop/CoreShop/pull/2218)
- [CoreBundle] Fix Grid Column Config (https://github.com/coreshop/CoreShop/pull/2215)
- [CoreBundle] Improve payment detail rendering (https://github.com/coreshop/CoreShop/pull/2207)
- [CoreBundle] Move Cart Subtotal Calculation To Dedicated Cart Processor (https://github.com/coreshop/CoreShop/pull/2205)
- [FrontendBundle] fix only showing customer orders and not cars too (https://github.com/coreshop/CoreShop/pull/2201)
- [IndexBundle] check recursively child elements (https://github.com/coreshop/CoreShop/pull/2200)
- [CoreBundle] set corret store value attributes (https://github.com/coreshop/CoreShop/pull/2197)
- [Core] allow decoration of StackRepository (https://github.com/coreshop/CoreShop/pull/2196)
- [CoreBundle] surcharge is possible to be more than item value (https://github.com/coreshop/CoreShop/pull/2192)
- [ShippingBundle] allow min.amount from to be 0 (https://github.com/coreshop/CoreShop/pull/2195)
- [Payum] don't use dev version (https://github.com/coreshop/CoreShop/pull/2193)

### 3.0.3

- Feature
    - [StorageList] allow for shareable StorageLists (eg. wishlist) (https://github.com/coreshop/CoreShop/pull/2150)

- Bugs
    - [StorageListItem] add definitive StorageList (Order/Wishlist) field to Item (https://github.com/coreshop/CoreShop/pull/2117)
    - [FrontendBundle] keep a reference to the just-removed Product (https://github.com/coreshop/CoreShop/pull/2125)
    - [MessengerBundle] only allow to list ListableReceiverInterface (https://github.com/coreshop/CoreShop/pull/2127)
    - [MessengerBundle] standalone fixes (https://github.com/coreshop/CoreShop/pull/2130)
    - [Slug] improve slug generation and don't store slugs for every site if the same (https://github.com/coreshop/CoreShop/pull/2131)
    - [MoneyBundle] fix issue with not-nullable types and null values (https://github.com/coreshop/CoreShop/pull/2138)
    - [Resource] add return types for BigIntIntegerType (https://github.com/coreshop/CoreShop/pull/2140)
    - [LinkGeneration] introduce possibility to disable slugs and use fallback routes (https://github.com/coreshop/CoreShop/pull/2143)
    - [Payum] use stable payum release (https://github.com/coreshop/CoreShop/pull/2141)
    - [FrontendBundle] use asset() helper for logo image (https://github.com/coreshop/CoreShop/pull/2137)
    - [CoreBundle] Fix migration for price_rules in OrderItem class (https://github.com/coreshop/CoreShop/pull/2145)
    - [CoreBundle] Add layout price_rules only once to OrderItem (https://github.com/coreshop/CoreShop/pull/2148)
    - [StorageList & Slug] fixes for shared wishlist & slugs (https://github.com/coreshop/CoreShop/pull/2151)
    - [CoreBundle] Use valid key in user migration (https://github.com/coreshop/CoreShop/pull/2159)
    - [CoreBundle] fix price_rule migration section (https://github.com/coreshop/CoreShop/pull/2160)
    - [StorageList] Fix SessionStorageManager (https://github.com/coreshop/CoreShop/pull/2165)
    - [IndexBundle] check if index value is null before exploding (https://github.com/coreshop/CoreShop/pull/2163)
    - [IndexBundle] process children within same Handler (https://github.com/coreshop/CoreShop/pull/2171)

### 3.0.2

> Please make sure you also start the messenger worker for the CoreShop Tasks
> ```bin/console messenger:consume coreshop_notification coreshop_index --time-limit=300```

- Features
    - [Messenger] introduce Messenger Bundle (https://github.com/coreshop/CoreShop/pull/2105, https://github.com/coreshop/CoreShop/pull/2114, https://github.com/coreshop/CoreShop/pull/2112, https://github.com/coreshop/CoreShop/pull/2106)

- Bugs
    - [CartPiceRules] fix OrderItem not finding Order with CartItemPriceRules (https://github.com/coreshop/CoreShop/pull/2116)
    - [StorageList] fix storage-list priority (https://github.com/coreshop/CoreShop/pull/2113)
    - [ResourceBundle] class names with lower-case names (https://github.com/coreshop/CoreShop/pull/2097)
    - [Checkout] introduce Payment Provider Validator to check if the selected one is still valid (https://github.com/coreshop/CoreShop/pull/2111)
    - [Unit] fix issues with unit selection in ui (https://github.com/coreshop/CoreShop/pull/2104)
    - [CustomerAssignment] Fix typo when passing the company ID to the router (https://github.com/coreshop/CoreShop/pull/2102)
    - [CoreBundle] fix cart-item-rule discount action form (https://github.com/coreshop/CoreShop/pull/2101)
    - [IndexBundle] fix: index table o_classId type fix (https://github.com/coreshop/CoreShop/pull/2095)
    - [Menu] Use correct permission definition for product unit menu item (https://github.com/coreshop/CoreShop/pull/2094)

## 3.0.1

- Bugs
    - [Translations] fix order object translations (https://github.com/coreshop/CoreShop/pull/2091)
    - [Psalm] fixes (https://github.com/coreshop/CoreShop/pull/2089)
    - [CustomerTransformHelper] Use company's name initial as parent folder for companies (https://github.com/coreshop/CoreShop/pull/2082)
    - [StorageListBundle] PimcoreStorageListRepository: Comply to PSR-4 autoloading standards (https://github.com/coreshop/CoreShop/pull/2081)
    - [ProductVariantTrait] Prevent MySQL syntax error (https://github.com/coreshop/CoreShop/pull/2086)
    - [Wishlist] add tests and fix routing (https://github.com/coreshop/CoreShop/pull/2084)

## 3.0.0

CoreShop is now Licenced under CCL and GPLv3! (https://github.com/coreshop/CoreShop/pull/2061)

- Feature
    - [IndexBundle] clone index, change default name of cloned item (https://github.com/coreshop/CoreShop/pull/2056)
    - [CartPriceRules] introduce feature to allow cart-price rules based on cart-items (https://github.com/coreshop/CoreShop/pull/2057, https://github.com/coreshop/CoreShop/pull/2060)
    - [Wishlist] Introduce a persisted wishlist - StorageListBundle now works as a base for Order and Wishlist (https://github.com/coreshop/CoreShop/pull/2030, https://github.com/coreshop/CoreShop/pull/2066)
    - [Reports] Support filtering for order type (https://github.com/coreshop/CoreShop/pull/2055)
    - [Symfony] fix Injecting @session is deprecated with Symfony (https://github.com/coreshop/CoreShop/pull/2035)
    - [AccessManagement] prepare CoreShop for advanced access-management (https://github.com/coreshop/CoreShop/pull/2063)
    - [Pimcore] 10.5 as min requirement (https://github.com/coreshop/CoreShop/pull/2067)

- Bugs
    - [VariantBundle] Serializer: Allow $innerObject to be null (https://github.com/coreshop/CoreShop/pull/2058, https://github.com/coreshop/CoreShop/pull/2069)
    - [DataHub] Fix non unique typename (https://github.com/coreshop/CoreShop/pull/2004)
    - [Translations] Update admin-translations.yml (https://github.com/coreshop/CoreShop/pull/2064)
    - [Pimcore UI] Make default Product Unit unselectable (https://github.com/coreshop/CoreShop/pull/2065)
    - [Variant] allow recursive attributes and variants (https://github.com/coreshop/CoreShop/pull/2068)

### 3.0.0-beta.5
> This will be the last BETA for the final release.

- Bugs
    - [Frontend] fix controller overwriting (https://github.com/coreshop/CoreShop/pull/2017)
    - Replaced old Registration Service with Customer Manager (https://github.com/coreshop/CoreShop/pull/2020)
    - Update install guide while it is in beta (https://github.com/coreshop/CoreShop/pull/2019)
    - [Frontend] fix getQuantityModifier return type (https://github.com/coreshop/CoreShop/pull/2024)
    - Fix TagManagerEnhancedEcommerce (https://github.com/coreshop/CoreShop/pull/2027)
    - [ProductBundle] fix saving of Price Rule Conditions and Actions when creating (https://github.com/coreshop/CoreShop/pull/2029)
    - [FrontendBundle] Consider UrlSlugs in the locale switcher (https://github.com/coreshop/CoreShop/pull/2032)
    - [DB] Remove Migrate and ClassUpdate rename and fix psalm issues (https://github.com/coreshop/CoreShop/pull/2034)
    - fix: property must not be accessed before initialization (https://github.com/coreshop/CoreShop/pull/2036)
    - [MoneyBundle] bugfix unmarshalVersion for coreShopMoney (https://github.com/coreshop/CoreShop/pull/2037)
    - [ThemeBundle] add document pre_renderer listener to resolve theme (https://github.com/coreshop/CoreShop/pull/2041)
    - [OrderBundle] fix "coreshop_admin_order_find" route (https://github.com/coreshop/CoreShop/pull/2045)
    - [Pimcore] add tests for ^10.5 (https://github.com/coreshop/CoreShop/pull/2043)
    - [Events] fix pimcore events BC break (https://github.com/coreshop/CoreShop/pull/2046)
    - [Tests] test against pimcore ^11.0 (https://github.com/coreshop/CoreShop/pull/2047)
    - [CoreBundle] add typecasts for MoneyFormatter in Reports, bugfix SQL (https://github.com/coreshop/CoreShop/pull/2048)
    - [MoneyBundle] bugfix marshalVersion for coreShopMoney (https://github.com/coreshop/CoreShop/pull/2051)
    - [MoneyBundle] bugfix setter code for FieldCollection (https://github.com/coreshop/CoreShop/pull/2052)
    - [CoreBundle] check for null value in CartStockAvailabilityValidator (https://github.com/coreshop/CoreShop/pull/2053)

### 3.0.0-beta.4
- Feature
    - [Variants] introduce Variant Bundle (https://github.com/coreshop/CoreShop/pull/1990) @breakone
    - [Pimcore] require min 10.4 (https://github.com/coreshop/CoreShop/pull/2013)

- Bugs
    - [Store] add Store Resolver for document save from Pimcore Admin (https://github.com/coreshop/CoreShop/pull/1962)
    - [ResourceBundle] add feature to clone resources (https://github.com/coreshop/CoreShop/pull/1965)
    - [CartPriceRules] fix cart-price-rules with over 100% discount (https://github.com/coreshop/CoreShop/pull/1966)
    - [Tests] re-enable cart tests (https://github.com/coreshop/CoreShop/pull/1970)
    - [FrontendBundle] fix: paginator.html.twig prepends four spaces to URLs (https://github.com/coreshop/CoreShop/pull/1968)
    - [IndexBundle] fix Argument #2 ($values) must be of type array, string given (https://github.com/coreshop/CoreShop/pull/1967)
    - [CoreBundle] use themeHelper to resolve template in StoreMailActionProcessor (https://github.com/coreshop/CoreShop/pull/1973)
    - [Condition] fix rules being active even if inactive when having no conditions (https://github.com/coreshop/CoreShop/pull/1977)
    - [CoreBundle] Handle null addresses when persisting customers (https://github.com/coreshop/CoreShop/pull/1979)
    - [ResourceBundle] fix ResourceSettingsController getConfigAction (https://github.com/coreshop/CoreShop/pull/1981)
    - [CoreBundle] fix registered user validator (https://github.com/coreshop/CoreShop/pull/1980)
    - [CustomerBundle] fix: wiring non-existing User classes which were migrated to CustomerBundle (https://github.com/coreshop/CoreShop/pull/1984)
    - [CoreBundle] Remove duplicate assignment (https://github.com/coreshop/CoreShop/pull/1985)
    - [CoreBundle] Fix Typo in notifcation.yml (https://github.com/coreshop/CoreShop/pull/1988)
    - [ResourceBundle] Fix grid view for orders/quotes/carts in admin (https://github.com/coreshop/CoreShop/pull/1989)
    - [OrderBundle] Fix for order grid configs (https://github.com/coreshop/CoreShop/pull/1992)
    - [Taxation] pass context into TaxCalculatorFactory (https://github.com/coreshop/CoreShop/pull/1978)
    - [CoreBundle] use TaxationDisplayProvider in CarrierChoiceType (https://github.com/coreshop/CoreShop/pull/1994)
    - [CoreBundle] Fix newsletter double opt in mail not sending (https://github.com/coreshop/CoreShop/pull/1993)
    - [CoreBundle] added newline before phoneNumber in CountryFixture (https://github.com/coreshop/CoreShop/pull/1995)
    - [CoreBundle] fix variant js, added variant select template (https://github.com/coreshop/CoreShop/pull/1997)
    - [Checkout] change thank-you to work with token and fix strict samesite cookies (https://github.com/coreshop/CoreShop/pull/1999)
    - [Variant] add concrete return type to ui configuration (https://github.com/coreshop/CoreShop/pull/1996)
    - [CoreBundle] fix event listeners for variants (https://github.com/coreshop/CoreShop/pull/2000)
    - [Rule] use tags for TraceableRuleConditionsValidationProcessor (https://github.com/coreshop/CoreShop/pull/2002)
    - [Guest] improve guest checkout to change address or use different one for shipping (https://github.com/coreshop/CoreShop/pull/2003)
    - [CoreBundle] use the index for categories to allow fine tuning the menus (https://github.com/coreshop/CoreShop/pull/1915)
    - [Pimcore] add dirname() and basename() to expression function provider (https://github.com/coreshop/CoreShop/pull/2007)
    - [Product Model] Allow getIndexableName() to return null (https://github.com/coreshop/CoreShop/pull/2009)
    - [Doctrine] Use doctrine-extension 3.6.0 (https://github.com/coreshop/CoreShop/pull/2012)

### 3.0.0-beta.3
- [IndexBundle] fix: generate the correct menu route (https://github.com/coreshop/CoreShop/pull/1815)
- [All] rename document coreshop editables (https://github.com/coreshop/CoreShop/pull/1822)
- [CoreBundle] Fix fetch of checkout_finisher url from request (https://github.com/coreshop/CoreShop/pull/1814)
- [FrontendBundle] Fix "Invoice Address is Shipping Address" in checkout address step (https://github.com/coreshop/CoreShop/pull/1823)
- [CoreBundle] Add address parent check (https://github.com/coreshop/CoreShop/pull/1825)
- [Core] add is_null in isNewEntity for Customer (https://github.com/coreshop/CoreShop/pull/1830)
- [IndexBundle] add boolean filter condition (https://github.com/coreshop/CoreShop/pull/1834)
- [IndexBundle] add clear button for filter preSelect combos (https://github.com/coreshop/CoreShop/pull/1833)
- [OrderBundle] fix session cart subscriber when no session is available (https://github.com/coreshop/CoreShop/pull/1836)
- [OrderBundle] cart context returns latest cart even if multiple found (https://github.com/coreshop/CoreShop/pull/1800)
- [ResourceBundle] fix resource list calling Pimcore Event and CoreShop Event to open object (https://github.com/coreshop/CoreShop/pull/1838)
- [CoreBundle] Fix doctrine type in migration (followup of #1839) (https://github.com/coreshop/CoreShop/pull/1840)
- [List] fix open CoreShop Entry and Pimcore DataObject (https://github.com/coreshop/CoreShop/pull/1845)
- [Cache] optimize Pimcore cache with doctrine entities (https://github.com/coreshop/CoreShop/pull/1843)
- [FrontendBundle] fix: Since symfony/http-kernel 5.1: Referencing controllers with a single colon is deprecated (https://github.com/coreshop/CoreShop/pull/1848/commits)
- [IndexBundle] Migrate to SettingsStoreAwareInstaller (https://github.com/coreshop/CoreShop/pull/1847)
- [FrontendBundle] Make category sort options configurable (https://github.com/coreshop/CoreShop/pull/1850)
- [Theme] fallback to SettableThemeContext (https://github.com/coreshop/CoreShop/pull/1851)
- [PimcoreBundle] added SluggableSlugger for SluggableListener (https://github.com/coreshop/CoreShop/pull/1857)
- [Pimcore] Definition Updater: check if we should use 'childs' or 'children' (https://github.com/coreshop/CoreShop/pull/1858)
- [Core] address assignment manager should check for null (https://github.com/coreshop/CoreShop/pull/1863)
- [IndexBundle] decimal should be 10,2 (https://github.com/coreshop/CoreShop/pull/1862)
- [IndexBundle] QuantityValue ID's are strings (https://github.com/coreshop/CoreShop/pull/1861)
- [IndexBundle] Add pimcore.dataobject.postAdd event to index DataObjects (https://github.com/coreshop/CoreShop/pull/1866)
- [PaymentBundle] Added payum payment model fields to coreshop payment model (https://github.com/coreshop/CoreShop/pull/1854)
- [Taxation] add taxRate to TaxItemInterface (https://github.com/coreshop/CoreShop/pull/1867)
- [CoreBundle] fix quote notification sending (https://github.com/coreshop/CoreShop/pull/1868)
- [CoreBundle] fix quote notification sending 3.x (https://github.com/coreshop/CoreShop/pull/1869)
- [FrontendBundle] fix createQuoteAction (https://github.com/coreshop/CoreShop/pull/1870)
- [IndexBundle] FIX:double click on field group adds them to indices (https://github.com/coreshop/CoreShop/pull/1871)
- [CoreBundle] fix user-reset password notification sending (https://github.com/coreshop/CoreShop/pull/1877)
- [IndexBundle] FIX: localized fields from brick and fieldcollections can be added to index (https://github.com/coreshop/CoreShop/pull/1872)
- [CurrencyBundle] fix exchange rate saving/deleting (https://github.com/coreshop/CoreShop/pull/1879)
- [PaymentBundle] FIX: coreshop_payment_provider editable select, wrong property name (https://github.com/coreshop/CoreShop/pull/1883)
- [ResourceBundle] Make stack of classes not extended in CoreBundle working (https://github.com/coreshop/CoreShop/pull/1882)
- [IndexBundle] FIX: select from multiselect throws error if no value is pre-selected (https://github.com/coreshop/CoreShop/pull/1880)
- [ThemeBundle] improve loading theme when document cannot be loaded (https://github.com/coreshop/CoreShop/pull/1884)
- [ResourceBundle] fix entity-merger to delete collection entries (https://github.com/coreshop/CoreShop/pull/1887)
- [ProductBundle] fix translations (https://github.com/coreshop/CoreShop/pull/1888)
- [IndexBundle] fixed issue 1891, fix totalCount for pagination (https://github.com/coreshop/CoreShop/pull/1892)
- [UserBundle] Email field required on request reset password form (https://github.com/coreshop/CoreShop/pull/1893)
- [FrontendBundle] Category select template fix (https://github.com/coreshop/CoreShop/pull/1890)
- [IndexBundle] category multiselect (https://github.com/coreshop/CoreShop/pull/1899)
- [IndexBundle] extending condition proccesor adds empty tag (https://github.com/coreshop/CoreShop/pull/1900)
- [OrderBundle] Error during serialization of OrderInvoice (https://github.com/coreshop/CoreShop/pull/1903)
- [CoreBundle] don't load settings if user has no permission (https://github.com/coreshop/CoreShop/pull/1902)
- [NotificationBundle] Fixed invoice, payment, shipment state condition in notifications (https://github.com/coreshop/CoreShop/pull/1905)
- [CoreBundle] Order mail note fix (https://github.com/coreshop/CoreShop/pull/1906)
- [ShippingBundle] Removed free shipping checkbog from carrier (https://github.com/coreshop/CoreShop/pull/1910)
- [FrontendBundle] logo in _header.html.twig no longer hardcoded (https://github.com/coreshop/CoreShop/pull/1908)
- [ProductBundle] duplicated unit definition title in product view in admin (https://github.com/coreshop/CoreShop/pull/1894/files)
- [OrderBundle] Fix xvfb error (https://github.com/coreshop/CoreShop/pull/1911)
- [FrontendBundle] fix: paginator prev/next links point to first/last page (https://github.com/coreshop/CoreShop/pull/1923)
- [CoreBundle] fix currency conversion (https://github.com/coreshop/CoreShop/pull/1889)
- [FrontendBundle] fix: use only first-level categories in CategoryController::menuAction (https://github.com/coreshop/CoreShop/pull/1914)
- [FrontendBundle] fix configuration for controller names (https://github.com/coreshop/CoreShop/pull/1919)
- [IndexBundle] don't lower-case interpreter types (https://github.com/coreshop/CoreShop/pull/1925)
- [IndexBundle] Category multiselect filter condition (https://github.com/coreshop/CoreShop/pull/1909)
- [PimcoreBundle] make command "coreshop:app:migration:generate" not hidden (https://github.com/coreshop/CoreShop/pull/1927)
- [Installer] mark migrations as migrated in the installer (https://github.com/coreshop/CoreShop/pull/1928)
- [IndexBundle] fix saving quantity values for index conditions ([IndexBundle] fix saving quantity values for index conditions)
- [IndexBundle] Search filter (https://github.com/coreshop/CoreShop/pull/1924)
- [NotificationBundle] fix reloading of Notification Conditions/Actions (https://github.com/coreshop/CoreShop/pull/1930)
- [Slug] fallback to ID if nameForSlug is null (https://github.com/coreshop/CoreShop/pull/1932)
- [ResourceBundle] fix creating static routes (https://github.com/coreshop/CoreShop/pull/1934)
- [FrontendBundle] Cart update and checkout validation for cart (https://github.com/coreshop/CoreShop/pull/1920)
- [FrontendBundle] fix styling of reset-password-request submit button (https://github.com/coreshop/CoreShop/pull/1935)
- [ProductBundle] fix ProductUnitDefinition unmarshal (https://github.com/coreshop/CoreShop/pull/1936)
- [Routing] fix route name coreshop_cart_create_quote (https://github.com/coreshop/CoreShop/pull/1937)
- [IndexBundle] Search filter dynamic name (https://github.com/coreshop/CoreShop/pull/1938)
- [IndexBundle] Fixed wrong categories returned when concatenator is AND (https://github.com/coreshop/CoreShop/pull/1939)
- [CoreBundle] Profiler fix (https://github.com/coreshop/CoreShop/pull/1941)
- [FrontendBundle] Fix redirect to profile if customer is present (https://github.com/coreshop/CoreShop/pull/1942/files)
- [MoneyBundle] allow Money to be nullable (https://github.com/coreshop/CoreShop/pull/1949)
- [Pimcore] use min Pimcore 10.3 and fix tests ([Pimcore] use min Pimcore 10.3 and fix tests)
- [Quotes] introduce simple state machine (https://github.com/coreshop/CoreShop/pull/1948)
- [FrontendBundle] Add submit buttons for voucher submit and form update in cart form (https://github.com/coreshop/CoreShop/pull/1950)
- [NotificationBundle] fix saving multiple emails (https://github.com/coreshop/CoreShop/pull/1954)
-


### 3.0.0-beta.2

- [Order] remove unused CartRepository (https://github.com/coreshop/CoreShop/pull/1801)
- [PimcoreBundle] add coreshop:migration:migrate and coreshop:migration:generate (https://github.com/coreshop/CoreShop/pull/1802)
- [FrontendBundle] fix new form namespace (https://github.com/coreshop/CoreShop/pull/1807)
- [Index] make ListingInterface a Pimcore PaginateListingInterface (https://github.com/coreshop/CoreShop/pull/1790)
- [Cart] fix existing cart initialization on customer login (https://github.com/coreshop/CoreShop/pull/1779)
- [CoreBundle] fix saving stores in PaymentProvider (https://github.com/coreshop/CoreShop/pull/1783)
- [Index] make IndexProcess compatible with the interface (https://github.com/coreshop/CoreShop/pull/1782)
- [User] remove md5 password and use password_hash (https://github.com/coreshop/CoreShop/pull/1780)
- [ThemeBundle] refactor theme-context to work with area-bricks (https://github.com/coreshop/CoreShop/pull/1778)
- [ThemeBundle] remove sylius theme-aware-translator, that doesn't work well with Pimcore (https://github.com/coreshop/CoreShop/pull/1777)
- [ResourceBundle] allow easier custom resources (https://github.com/coreshop/CoreShop/pull/1776)
- [Index] remove dbal connection in AbstractListing (https://github.com/coreshop/CoreShop/pull/1769)
- [Store select / multiselect] Support getOptions() via option provider (https://github.com/coreshop/CoreShop/pull/1773)
- [CoreExtensions] refactor how Doctrine Entities are cloned (https://github.com/coreshop/CoreShop/pull/1770)
- [Faker] use fakerphp/faker (https://github.com/coreshop/CoreShop/pull/1768)
- [CoreBundle] remove duplicate paymentTotal and convertedPaymentTotal from class definition (https://github.com/coreshop/CoreShop/pull/1766)
- [OrderBundle] fix admin en translations (https://github.com/coreshop/CoreShop/pull/1764)
- [All] remove installed translations and use symfony translations instead (https://github.com/coreshop/CoreShop/pull/1762)

### 3.0.0-beta.1

- PHP8.0 Return Types (https://github.com/coreshop/CoreShop/pull/1288, https://github.com/coreshop/CoreShop/pull/1666)
- Cart eq Order eq Quote - one Object to rule them all (https://github.com/coreshop/CoreShop/pull/1289)
- Strict Types (https://github.com/coreshop/CoreShop/pull/1294)
- make service-aliases deprecated and change all internal uses of it (https://github.com/coreshop/CoreShop/pull/1320)
- change IndexableInterface and pass IndexInterface (https://github.com/coreshop/CoreShop/pull/1326)
- remove php template helpers (https://github.com/coreshop/CoreShop/pull/1323)
- [Panther] Implement ui-tests (https://github.com/coreshop/CoreShop/pull/1335, https://github.com/coreshop/CoreShop/pull/1347)
- introduce class translations (https://github.com/coreshop/CoreShop/pull/1349)
- change cart/order base-currency conversion (https://github.com/coreshop/CoreShop/pull/1324)
- Allow to create a new Customer within the order-creation Process (https://github.com/coreshop/CoreShop/pull/1236)
- introduce currency fraction display provider service (https://github.com/coreshop/CoreShop/pull/1394)
- introduce tax-display service (https://github.com/coreshop/CoreShop/pull/1393)
- integration to dachcom-digital/pimcore-seo (https://github.com/coreshop/CoreShop/pull/1399)
- remove usage of ItemKeyTransformer Service and use DataObject\Service directly (https://github.com/coreshop/CoreShop/pull/1411)
- create default address if customer doesn't have one (https://github.com/coreshop/CoreShop/pull/1435)
- apply confirm and pay transition for orders with value of 0 (https://github.com/coreshop/CoreShop/pull/1434)
- resolve theme only if not in admin (https://github.com/coreshop/CoreShop/pull/1505)
- Pimcore X Compatibility (https://github.com/coreshop/CoreShop/pull/1511, https://github.com/coreshop/CoreShop/pull/1574, https://github.com/coreshop/CoreShop/pull/1599, https://github.com/coreshop/CoreShop/pull/1621)
- migrate to sylius/theme-bundle (https://github.com/coreshop/CoreShop/pull/1513)
- implement new JS Routing and start with first backend tests (https://github.com/coreshop/CoreShop/pull/1420)
- some JMS fixes and payum concurrency test (https://github.com/coreshop/CoreShop/pull/1550)
- cleanup proposal stuff and fix serialization of Doctrine collections (https://github.com/coreshop/CoreShop/pull/1641)
- migrate migrations to Doctrine Migrations Bundle (https://github.com/coreshop/CoreShop/pull/1635)
- Feature/customer list (https://github.com/coreshop/CoreShop/pull/1667)
- Fix merge for index-conditions (https://github.com/coreshop/CoreShop/pull/1673)
- fix voucher modifier with empty voucher code (https://github.com/coreshop/CoreShop/pull/1672)
- [ResourceBundle] fix unserialization of CoreShop entities saved by pimcore auto save (https://github.com/coreshop/CoreShop/pull/1674)
- split customer and user into seperate entities (https://github.com/coreshop/CoreShop/pull/1669)
- add proper events for cart-item add and remove (https://github.com/coreshop/CoreShop/pull/1676)
- Introduce a folder creation service which loads the paths directly from the metadata (https://github.com/coreshop/CoreShop/pull/1677)
- Introduce payum payment bundle (https://github.com/coreshop/CoreShop/pull/1675)
- [Slug] default generate slugs and use instead of static routes for product and category (https://github.com/coreshop/CoreShop/pull/1678, https://github.com/coreshop/CoreShop/pull/1701)
- [FrontendBundle] Macro "price" is not defined in template (https://github.com/coreshop/CoreShop/pull/1684)
- [SEO - ImageExtractor] Add thumbnail definition coreshop_seo (https://github.com/coreshop/CoreShop/pull/1688)
- [Shipping] Ability to hide carrier from checkout (https://github.com/coreshop/CoreShop/pull/1693)
- [Psalm] Introduce Psaml Tests for Components (https://github.com/coreshop/CoreShop/pull/1727)
- Removed security.yaml, since Pimcore 10, you have to define the security config yourself, just copy following to config/packages/security.yaml (https://github.com/coreshop/CoreShop/pull/1599)

```yaml
parameters:
    coreshop.security.frontend_regex: "^/(?!admin)[^/]++"

security:
    providers:
        coreshop_customer:
            id: CoreShop\Bundle\CoreBundle\Security\ObjectUserProvider
    firewalls:
        coreshop_frontend:
            anonymous: ~
            provider: coreshop_customer
            pattern: '%coreshop.security.frontend_regex%'
            context: shop
            form_login:
                login_path: coreshop_login
                check_path: coreshop_login_check
                provider: coreshop_customer
                failure_path: coreshop_login
                default_target_path: coreshop_index
                use_forward: false
                use_referer: true
            remember_me:
                secret: "%secret%"
                name: APP_CORESHOP_REMEMBER_ME
                lifetime: 31536000
                remember_me_parameter: _remember_me
            logout:
                path: coreshop_logout
                target: coreshop_login
                invalidate_session: false
                success_handler: CoreShop\Bundle\CoreBundle\EventListener\ShopUserLogoutHandler

    access_control:
        - { path: "%coreshop.security.frontend_regex%/_partial", role: IS_AUTHENTICATED_ANONYMOUSLY, ips: [127.0.0.1, ::1] }
        - { path: "%coreshop.security.frontend_regex%/_partial", role: ROLE_NO_ACCESS }

```


## 2.2.x

### 2.2.14
- Bugs:
    - [ProductQuantityPriceRules] remove variable variables sign (https://github.com/coreshop/CoreShop/pull/1991)

### 2.2.13
- Bugs:
    - [ResourceBundle] fix ResourceSettingsController getConfigAction (https://github.com/coreshop/CoreShop/pull/1981)

### 2.2.12
- Bugs:
    - [FrontendBundle] Fix "Invoice Address is Shipping Address" in checkout address step (https://github.com/coreshop/CoreShop/pull/1823)
    - [OrderBundle] cart context returns latest cart even if multiple found (https://github.com/coreshop/CoreShop/pull/1837)
    - [FrontendBundle] Password reset: Clearer form labels (https://github.com/coreshop/CoreShop/pull/1855)
    - [ResourceBundle] Fatal error when saving object with product-specific prices -> EntityMerger got object of wrong class (https://github.com/coreshop/CoreShop/pull/1864)
    - [ProductBundle] Add isEqual() for product-specific price (https://github.com/coreshop/CoreShop/pull/1844)
    - [Pimcore] Fix delete icons in Pimcore backend (https://github.com/coreshop/CoreShop/pull/1885)

### 2.2.11
- Bugs:
    - [CoreExtensions] refactor how Doctrine Entities are cloned (https://github.com/coreshop/CoreShop/pull/1770)
    - [Frontend] After renaming formtypes with coreshop[], form elements where not found (https://github.com/coreshop/CoreShop/pull/1791)
    - [Checkout] fix new form namespace (https://github.com/coreshop/CoreShop/pull/1807)

### 2.2.10
- Bugs:
    - [Shipping] make shipping calculation more independent from cart (https://github.com/coreshop/CoreShop/pull/1562)
    - [PaymentBundle] Add PaymentProvider Multiselect CoreExtension (https://github.com/coreshop/CoreShop/pull/1585)
    - [Address] prevent empty lines in address formatter (https://github.com/coreshop/CoreShop/pull/1587/files)
    - [Core] remove country-aware currency context as it messes with the Storage Based Currency Context (https://github.com/coreshop/CoreShop/pull/1588)
    - [DoctrineCache] remove doctrine cache and all usages of useQueryCache and useResultCache (https://github.com/coreshop/CoreShop/pull/1596)
    - [FrontendBundle] use named forms where applicable (https://github.com/coreshop/CoreShop/pull/1597)
    - [All] Add italian translations for validators (https://github.com/coreshop/CoreShop/pull/1595)
    - [PaymentBundle] Fix provider multiselect (https://github.com/coreshop/CoreShop/pull/1601)
    - [IndexBundle] Fix range filter to work if min/max value = 0 (https://github.com/coreshop/CoreShop/pull/1606)
    - [Docs] Fixed typo (https://github.com/coreshop/CoreShop/pull/1608)
    - [FrontendBundle] clear password reset hash after reset (https://github.com/coreshop/CoreShop/pull/1631)
    - [ShippingBundle] fix configuration (https://github.com/coreshop/CoreShop/pull/1632)
    - [PimcoreBundle] fix missing Multiselect (https://github.com/coreshop/CoreShop/pull/1614)
    - [Fixtures] fix region_short for address format (https://github.com/coreshop/CoreShop/pull/1636)
    - [Docs] Add missing process conditions in docs (https://github.com/coreshop/CoreShop/pull/1643)
    - [FrontendBundle] fix having unique form-ids for cart add (https://github.com/coreshop/CoreShop/pull/1648)
    - [Core] fix cloning and serializing (with DeepCopy) of UnitDefinitions (https://github.com/coreshop/CoreShop/pull/1649)
    - [Tracking] enable auto-configuration and auto-wiring with configuration (https://github.com/coreshop/CoreShop/pull/1656)
    - [Rule] refactor Rule conditions and actions persistence (https://github.com/coreshop/CoreShop/pull/1657)
    - [OrderBundle] fix pagination in voucher codes and add pagination for csv export (https://github.com/coreshop/CoreShop/pull/1662)
    - [CoreBundle] remove zones from shipping rules installer (https://github.com/coreshop/CoreShop/pull/1664)
    - [IndexBundle] add feature to rename tables when index get's renamed (https://github.com/coreshop/CoreShop/pull/1663)
    - [FrontendBundle] Print cart price rule label instead name if available (https://github.com/coreshop/CoreShop/pull/1668)
    - [Tests] Add Pimcore ~6.9.0 in behat test matrix (https://github.com/coreshop/CoreShop/pull/1670)
    - [CoreBundle] Use right permission for tax rate (https://github.com/coreshop/CoreShop/pull/1681)
    - [SEO] Add thumbnail definition coreshop_seo (https://github.com/coreshop/CoreShop/pull/1688)
    - [Core] Fix: ProductRepository: Added missing placeholder (https://github.com/coreshop/CoreShop/pull/1708)
    - [Docs] Quantity Price Rules (https://github.com/coreshop/CoreShop/pull/1716)
    - [PimcoreBundle] super-select box readonly (https://github.com/coreshop/CoreShop/pull/1739)

- Feature:
    - [ProductQuantityPriceRules] add interface for QuantityPriceFetcher and QuantityRuleFetcher (https://github.com/coreshop/CoreShop/pull/1628)
    - [Payment] make payment description translatable (https://github.com/coreshop/CoreShop/pull/1633)

### 2.2.9
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

### 2.2.8
- Bugs:
    - [OptimisticEntityLockBundle] fix Version loading with Composer v3 (https://github.com/coreshop/CoreShop/pull/1558)

### 2.2.7
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

### 2.2.6
- Bugs:
    - [Product] fix cloning of ProductUnitDefinitions and add test for it. (https://github.com/coreshop/CoreShop/pull/1502)
    - [CoreBundle] interactive login: cart might not be available for several reasons, ignore exception and don't assign a cart (https://github.com/coreshop/CoreShop/pull/1500)

### 2.2.5
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

### 2.2.4
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

### 2.2.3
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

### 2.2.2
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

### 2.2.1
- Bugs:
    - [CoreBundle] fix registration service (https://github.com/coreshop/CoreShop/pull/1391)
    - [CoreBundle] fix validation groups (https://github.com/coreshop/CoreShop/pull/1390)
    - [PimcoreBundle] Dynamic Dropdowns Issues (https://github.com/coreshop/CoreShop/issues/1380, https://github.com/coreshop/CoreShop/pull/1382)
    - [FrontendBundle] revert url-forward, doesn't work for all cases  (https://github.com/coreshop/CoreShop/pull/1386, https://github.com/coreshop/CoreShop/issues/1383)

### 2.2.0
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


### 2.2.0-RC.2
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

### 2.2.0
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


## 2.1.x

### 2.1.9
- Bugs:
    - [OrderBundle] fix usage of inherited values in backend-cart/order controllers (https://github.com/coreshop/CoreShop/pull/1461, https://github.com/coreshop/CoreShop/issues/1459)

### 2.1.8
- Bugs:
    - [ResourceBundle] Fix unique entity validator (https://github.com/coreshop/CoreShop/pull/1385)
    - [DataHub] fix integration (https://github.com/coreshop/CoreShop/pull/1389)

- Features:
    - [WorkflowBundle] Add enabled option to workflow callbacks (https://github.com/coreshop/CoreShop/pull/1392)

### 2.1.7
- Bugs:
    - [Pimcore] fix getDataForEditmode (https://github.com/coreshop/CoreShop/pull/1372, https://github.com/coreshop/CoreShop/pull/1361)

### 2.1.6
- Bugs:
    - [CoreBundle][2.1] Fix name of country combo (https://github.com/coreshop/CoreShop/pull/1350)

### 2.1.5
- Bugs:
    - [Translations] fix: add missing translation (https://github.com/coreshop/CoreShop/pull/1308)
    - [IndexBundle] index ui improvements (https://github.com/coreshop/CoreShop/pull/1300)
    - [ThemeBundle] fix configuration for default resolvers (https://github.com/coreshop/CoreShop/pull/1301)
    - [AddressBundle] introduce filter-active action and filter store-base countries by those (https://github.com/coreshop/CoreShop/pull/1302)

### 2.1.4
- Feature:
    - [CoreBundle] allow store-values to be reset and inherit again (https://github.com/coreshop/CoreShop/pull/1273)
- Bugs:
    - [IndexBundle] Change return type of WorkerInterface::getList (https://github.com/coreshop/CoreShop/pull/1280)
    - [ThemeBundle] fix default theme-resolvers (https://github.com/coreshop/CoreShop/pull/1281)
    - [Pimcore] make compatible with Pimcore 6.5 (https://github.com/coreshop/CoreShop/pull/1285)
    - [Core] fix bug where we calculated item-discount and item-discount-prices (https://github.com/coreshop/CoreShop/pull/1293)

### 2.1.3
- Bugs:
    - [Address, Order, Core] fix release (https://github.com/coreshop/CoreShop/pull/1269)

### 2.1.2
- Features:
    - [FrontendBundle] Change function from private to protected (https://github.com/coreshop/CoreShop/pull/1248)
    - [Installer] update logo (https://github.com/coreshop/CoreShop/pull/1264)
    - [Cart] introduce cart-context resolver to allow better extendability of the context used for the cart (https://github.com/coreshop/CoreShop/pull/1267)

- Bugs:
    - [ThemeBundle] fix Undefined index: default_resolvers (https://github.com/coreshop/CoreShop/pull/1235)
    - [IndexBundle]  $indexIds is always an array, hence the condition is now empty (https://github.com/coreshop/CoreShop/pull/1241)
    - [Stan] fixes for 2.1 (https://github.com/coreshop/CoreShop/pull/1244)
    - [CurrencyBundle] fix money-currency type is rounding prices wrong (https://github.com/coreshop/CoreShop/pull/1238)
    - [CoreBundle] Change repository so that unit definition deletion works with multiple product models (https://github.com/coreshop/CoreShop/pull/1252)
    - [Order] fix throwing/catching right exceptions in purchasable calculalator (https://github.com/coreshop/CoreShop/pull/1250)
    - [Core] allow non QuantityPriceRangeAware Products in cart-processor (https://github.com/coreshop/CoreShop/pull/1249)
    - [IndexBundle] fix index columns form (https://github.com/coreshop/CoreShop/pull/1259)
    - [Country] don't call the request based resolvers every time (https://github.com/coreshop/CoreShop/pull/1261)

### 2.1.1
- Features:
    - [CoreBundle] Implement Variant Unit and QPR Solidifier (https://github.com/coreshop/CoreShop/issues/1157)
    - [AddressBundle] Improve Country Address Formatting (https://github.com/coreshop/CoreShop/pull/1153)
    - [OrderBundle] properly implement AddMultipleToCart (https://github.com/coreshop/CoreShop/pull/1154)
    - [IndexBundle] allow to query relations also by relation type (https://github.com/coreshop/CoreShop/pull/1156)
    - [SEOBundle] add priority to extractors (https://github.com/coreshop/CoreShop/pull/1155)
    - [QuantityPriceRules] Allow Object Deletion without removing QPR first (https://github.com/coreshop/CoreShop/issues/1160)
    - [CoreBundle] Improve Unit Definition <=> QPR Dependency (https://github.com/coreshop/CoreShop/pull/1161)
    - [StorageList] introduce service to resolve if cart-items are equal (https://github.com/coreshop/CoreShop/pull/1188)
    - [OrderBundle] Allow Item Data per Row in Order Overview (https://github.com/coreshop/CoreShop/pull/1193)
    - [OrderBundle] add sale-detail event (https://github.com/coreshop/CoreShop/pull/1192)
    - [IndexBundle] Argument for re-index command (https://github.com/coreshop/CoreShop/pull/1219)
    - [CoreBundle] Maximum Quantity to Order (https://github.com/coreshop/CoreShop/issues/1209)

- Bugs:
    - [StoreBundle] add missing store dependency (https://github.com/coreshop/CoreShop/pull/1159)
    - [Install] add dummy migration (https://github.com/coreshop/CoreShop/pull/1172)
    - [CoreBundle] Remove Store Values after Store has been removed (https://github.com/coreshop/CoreShop/pull/1171)
    - [Order] Fix Character Length Count in Voucher Code Generator (https://github.com/coreshop/CoreShop/pull/1194/files)
    - [Order] fix item price for items without tax-rule (https://github.com/coreshop/CoreShop/pull/1200)
    - [TRACKING] use single item price in order item extractor (https://github.com/coreshop/CoreShop/pull/1232)

### 2.1.0
- If you have a custom validation File for *AddToCart* or *Cart*, make sure to use the new 2 MinimumQuantity and MaximumQuantity Constraints. Otherwise it will happen that a validation is triggered twice.

### 2.1.0
- Bugs:
    - [ThemeBundle] add missing dependency to pimcore-bundle (https://github.com/coreshop/CoreShop/pull/1138, https://github.com/coreshop/CoreShop/pull/1140)
    - [ResourceBundle] fix naming of parameter sortBy (https://github.com/coreshop/CoreShop/pull/1132)
    - [Quantity Price Rules] Check Inherited Product Quantity Price Range Data (https://github.com/coreshop/CoreShop/pull/1143)
    - [FrontendBundle] allow usage of auto-wired Frontend Controllers (https://github.com/coreshop/CoreShop/pull/1141)
    - [OrderBundle] CartItem Quantity has to be > 0 (https://github.com/coreshop/CoreShop/pull/1144)

### 2.1.0-rc.2
- Features:
    - [IndexBundle] allow for more complex doctrine types in index (https://github.com/coreshop/CoreShop/pull/1110)
    - [IndexBundle] add Select and Multiselect Filter Processor from Multiselect (https://github.com/coreshop/CoreShop/pull/1111)
    - [Autowire] improvement: allow service registries to be autowired (https://github.com/coreshop/CoreShop/pull/1113, https://github.com/coreshop/CoreShop/pull/1116, https://github.com/coreshop/CoreShop/pull/1122)
    - [QuantityPriceRules] allow price range ordering @solverat (https://github.com/coreshop/CoreShop/pull/1121)
    - [Payment] Fix payment provider logo @davidhoeck (https://github.com/coreshop/CoreShop/pull/1124)
    - [ResourceBundle, OrderBundle, CoreBundle] introduce more memory efficient (https://github.com/coreshop/CoreShop/pull/1126, https://github.com/coreshop/CoreShop/pull/1129)
    - [Shipping, ShippingBundle] Add logo field to carrier @davidhoeck (https://github.com/coreshop/CoreShop/pull/1127)
    -

- Bugs:
    - [Tests] exit code (https://github.com/coreshop/CoreShop/pull/1119)
    - [Tracking] remove decimal factor multiplier in order extractor @solverat (https://github.com/coreshop/CoreShop/pull/1128)
    - [QuantityPriceRules] Move migration for Quantity Rule Range Unit to Sales Unit migration  (https://github.com/coreshop/CoreShop/pull/1123)

### 2.1.0
- BC-Break: Introduced `array $options` parameter into `CoreShop\Component\Index\Listing\ListingInterface` to allow certain variations for loading data

- Introduced WholesalePrice Calculators, this deprecates the "wholesalePrice" property in the Product Class and adds the "wholesaleBuyingPrice" Property with a currency attached. We've added a migration for that, but since we need a currency now, we just assume the buying currency as the defaults store currency. If you have a different one, create a custom migration that changes it.

- `CoreShop\Component\StorageList\StorageListModifierInterface` got completely refactored and works a bit different now. Since deciding what StorageListItem belongs to what product, can be a bit more complicated, we decided to introduce a BC break.
    - `CoreShop\Component\StorageList\StorageListModifierInterface` added `addToList` function
    - `CoreShop\Component\StorageList\StorageListModifierInterface` removed `remove` to `removeFromList`
    - `CoreShop\Component\StorageList\Model\StorageListItemInterface` added `equals` function
    - `CoreShop\Component\StorageList\Model\StorageListInterface` removed `getItemForProduct` function
    - `CoreShop\Component\StorageList\Model\StorageListProductInterface` got deprecated, since not it's not needed anymore
- `CoreShop\Component\Order\Factory\CartItemFactoryInterface` introduced a new function `public function createWithPurchasable(PurchasableInterface $purchasable, $quantity = 1);`

- Introduced Theme-Bundle to handle Themes (https://github.com/coreshop/CoreShop/pull/749, https://github.com/coreshop/CoreShop/pull/756, https://github.com/coreshop/CoreShop/pull/755)
    - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelper.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelper](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelper.php)
    - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeHelperInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeHelperInterface.php)
    - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolver.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolver.php)
    - deprecated [CoreShop\Bundle\StoreBundle\Theme\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/StoreBundle/Theme/ThemeResolverInterface.php) in favor of [CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ThemeBundle/Service/ThemeResolverInterface.php)

- Introduce AddToCartFormType and QuantityType. This allows to use validators to check if its allowed to add a product to the cart. If you update from CoreShop 2.0.* change the add-to-cart form in your templates to the following: (https://github.com/coreshop/CoreShop/pull/812/files#diff-3e06a5f0e813be230a0cd232e916738eL29) (https://github.com/coreshop/CoreShop/pull/812, https://github.com/coreshop/CoreShop/pull/864)
    - `{{ render(url('coreshop_cart_add', {'product': product.id})) }}`
    - Be sure you have adopted the new form template in `views/Product/_addToCart.html.twig`

- Introduced Store Unit: (https://github.com/coreshop/CoreShop/pull/877, https://github.com/coreshop/CoreShop/pull/883, https://github.com/coreshop/CoreShop/pull/950, https://github.com/coreshop/CoreShop/pull/902, https://github.com/coreshop/CoreShop/pull/896)
    - Please add `product_unit` to permission table.
    - Remove `storePrice` field from all product classes
    - If you don't use the `Store Price` element in your classes besides the `storePrice` field, you should delete the `coreshop_product_store_price` table after migration.
    - We introduced a new jquery plugin `$.coreshopQuantitySelector()` which allows you to add more numeric control to your quantity field, checkout our demo [example](https://github.com/coreshop/CoreShop/blob/7d05ccd89aed99f9fd93c585e096cd1edaf20010/src/CoreShop/Bundle/FrontendBundle/Resources/public/static/js/shop.js#L20).

-  Features:
    - *Product Quantity Rules*, big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/791, https://github.com/coreshop/CoreShop/pull/954, https://github.com/coreshop/CoreShop/pull/951, https://github.com/coreshop/CoreShop/pull/898, https://github.com/coreshop/CoreShop/pull/813)
    - *Product Units*, big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/861, https://github.com/coreshop/CoreShop/pull/911, https://github.com/coreshop/CoreShop/pull/900, https://github.com/coreshop/CoreShop/pull/897, https://github.com/coreshop/CoreShop/pull/891, https://github.com/coreshop/CoreShop/pull/875)
    - *Minimum Order Quantity and Item Quantity Factor* big thanks to @solverat (https://github.com/coreshop/CoreShop/pull/881)
    - Introduce Menu Bundle (https://github.com/coreshop/CoreShop/pull/854, https://github.com/coreshop/CoreShop/pull/880, https://github.com/coreshop/CoreShop/pull/878, https://github.com/coreshop/CoreShop/pull/876)
    - Introduce Theme Bundle (https://github.com/coreshop/CoreShop/pull/749, https://github.com/coreshop/CoreShop/pull/756, https://github.com/coreshop/CoreShop/pull/755)
    - [Store Values] swap store-prices with store-values and make them extendable for custom store values (https://github.com/coreshop/CoreShop/pull/877, https://github.com/coreshop/CoreShop/pull/883, https://github.com/coreshop/CoreShop/pull/950, https://github.com/coreshop/CoreShop/pull/902, https://github.com/coreshop/CoreShop/pull/896)
    - [ResourceBundle] Add group to ThumbnailInstaller (https://github.com/coreshop/CoreShop/pull/1017) @AndiKeiser
    - [All] AutoWiring (https://github.com/coreshop/CoreShop/pull/850)
    - [All] Pimcore 6/Symfony 4 compatibilty (https://github.com/coreshop/CoreShop/pull/996, https://github.com/coreshop/CoreShop/pull/1062, https://github.com/coreshop/CoreShop/pull/1035)
    - [Tests] Update to friends-of-behat/symfony-extension:^2.0 (https://github.com/coreshop/CoreShop/pull/1024)
    - [Travis] add setup for Pimcore 6 changed system.yml config (https://github.com/coreshop/CoreShop/pull/1029)
    - [All] Introduce configurable decimal precision and factor (https://github.com/coreshop/CoreShop/pull/1030)
    - [All] change db type for pricing fields to BIGINT (https://github.com/coreshop/CoreShop/pull/1032, https://github.com/coreshop/CoreShop/pull/1098)
    - [ShippingBundle] introduce gross/net checkbox for amount condition (https://github.com/coreshop/CoreShop/pull/1042)
    - [Graphql] Add Support for Pimcore GraphQl Data Hub (https://github.com/coreshop/CoreShop/pull/1052)
    - [Tracking] Add Decimal Precision to Order Extractor (https://github.com/coreshop/CoreShop/pull/1058)
    - [MoneyBundle] add fallback in money-bundle for decimal precision (https://github.com/coreshop/CoreShop/pull/1061)
    - [Docs] Added visualization for coreshop_order Workflow (https://github.com/coreshop/CoreShop/pull/1067) @davidhoeck
    - [Pimcore] add conflict for Pimcore 6.1.0 and Pimcore 6.1.1 (https://github.com/coreshop/CoreShop/pull/1069)
    - [Docs] Update 03_Theme.md (https://github.com/coreshop/CoreShop/pull/1072) @D37R4C7
    - [FQCN] FQCN Services (https://github.com/coreshop/CoreShop/pull/1079, https://github.com/coreshop/CoreShop/pull/1084, https://github.com/coreshop/CoreShop/issues/1085, https://github.com/coreshop/CoreShop/pull/1086, https://github.com/coreshop/CoreShop/pull/1090)
    - [Product] Unit Definition - Precision (https://github.com/coreshop/CoreShop/pull/1081, https://github.com/coreshop/CoreShop/pull/1091, https://github.com/coreshop/CoreShop/pull/1092) @solverat
    - [IndexBundle] split conditions into pre_conditions and user_conditions (https://github.com/coreshop/CoreShop/pull/1055)
    - [Quantity Price Rules] Remove "to" field from quantity price range (https://github.com/coreshop/CoreShop/pull/1003, https://github.com/coreshop/CoreShop/pull/1095)
    - [Order] Introduce backend cart-creation and cart-details (https://github.com/coreshop/CoreShop/pull/963)
    - [PermissionSetup] add category to Permission (https://github.com/coreshop/CoreShop/pull/1101)
    - [ShippingBundle] add more carrier price options (https://github.com/coreshop/CoreShop/pull/1015)
    - [FrontendBundle] show discount/surcharge label in order overview (https://github.com/coreshop/CoreShop/pull/1006)
    - [Index, IndexBundle] allow options for the listing load function (https://github.com/coreshop/CoreShop/pull/1001)
    - [ResourceBundle] add connection interface into Pimcore Repository (https://github.com/coreshop/CoreShop/pull/1000)
    - [ProductBundle, CoreBundle] set itemQuantityFactor min value to null (https://github.com/coreshop/CoreShop/pull/993)
    - [Core] use default unit quanity in onhold inventory (https://github.com/coreshop/CoreShop/pull/990) @solverat
    - [Maintenance] refactor to use new maintenance task from pimcore 5.8 (https://github.com/coreshop/CoreShop/pull/986)
    - [All] require min Pimcore 5.8 and PHP 7.2 (https://github.com/coreshop/CoreShop/pull/973)
    - [CoreBundle] introduce store-preview for products (https://github.com/coreshop/CoreShop/pull/982)
    - [Adjustments] remove return type AdjustmentInterface (https://github.com/coreshop/CoreShop/pull/978) @solverat
    - [Taxation] fix tax collection on gross values - 2.1 (https://github.com/coreshop/CoreShop/pull/974)
    - [Order] introduce translatable cart-price-rules (https://github.com/coreshop/CoreShop/pull/969)
    - [WholesaleCalculator] introduce purchasable wholesale calculator (https://github.com/coreshop/CoreShop/pull/957)
    - [Product] introduce stop propagation flag for price-rules (https://github.com/coreshop/CoreShop/pull/946)
    - [Pimcore] make CoreShop 2.1 compatible with Pimcore 5.7.2 (https://github.com/coreshop/CoreShop/pull/915)
    - [CoreBundle] serialize relational values (product and store) as relation (https://github.com/coreshop/CoreShop/pull/916)
    - [Core] Disable Customer Deletion if bounded Orders are available (https://github.com/coreshop/CoreShop/pull/732)
    - [PriceRules] add priority to product-price-rules (https://github.com/coreshop/CoreShop/pull/905)
    - [Product] add translation to product price rules (https://github.com/coreshop/CoreShop/pull/879)
    - [All] Min 5.7 (https://github.com/coreshop/CoreShop/pull/871)
    - [Core] Refactor how we identify CartItem - Product (https://github.com/coreshop/CoreShop/pull/866)
    - [Core/Cart] Refactor add to cart (https://github.com/coreshop/CoreShop/pull/864)
    - [Cart] implement add-to-cart as Symfony Form (https://github.com/coreshop/CoreShop/pull/812)
    - [Core, Order, Product] throw exceptions for when a price can't be found (https://github.com/coreshop/CoreShop/pull/811)
    - [Order] make accessor protected for OrderDocument Processor (https://github.com/coreshop/CoreShop/pull/775)
    - [WorkflowBundle, OrderBundle] always load all available coreshop states into js (https://github.com/coreshop/CoreShop/pull/773)
    - [Core, Order] also apply discounts to cart-items (https://github.com/coreshop/CoreShop/pull/770)
    - [OrderBundle, Pimcore] extract DataLoader from Controller to be used oustide (https://github.com/coreshop/CoreShop/pull/771)
    - [OrderBundle] add event to prepare sale in order to better extend details (https://github.com/coreshop/CoreShop/pull/772)
    - [IndexBundle] implement optional inclusion into ProcessManager (https://github.com/coreshop/CoreShop/pull/758)

- Bugs:
    - [Product] remove getIsAvailableWhenOutOfStock and setIsAvailableWhenOutOfStock (https://github.com/coreshop/CoreShop/pull/1019)
    - [CoreBundle] fix inheritance for store-values (https://github.com/coreshop/CoreShop/pull/1028)
    - [ResourceBundle] Fix missing coreshop.helper namespace (https://github.com/coreshop/CoreShop/pull/1039)
    - [PimcoreBundle] fix dynamic-dropdown for pimcore-6 (https://github.com/coreshop/CoreShop/pull/1040)
    - [All] fix related to element.href and this pimcore PR: pimcore/pimcore#4496 (https://github.com/coreshop/CoreShop/pull/1041)
    - [Core] Store Values - default value to 0 instead of null, fix setting inherited store values (https://github.com/coreshop/CoreShop/pull/1093)
    - [FrontendBundle] fix wishlist remove and allow purchasables (https://github.com/coreshop/CoreShop/pull/997)
    - [Bundles] provide proper version strings and names (https://github.com/coreshop/CoreShop/pull/970)
    - [Migration] only add indices to store_price table if table actually exists (https://github.com/coreshop/CoreShop/pull/967)
    - [IndexBundle] improve standalone usage (https://github.com/coreshop/CoreShop/pull/965)
    - [Product] allow price rule labels to be null and fix error with two trait constructors (https://github.com/coreshop/CoreShop/pull/953)
    - [ProductBundle] re-add active to list serializer group (https://github.com/coreshop/CoreShop/pull/912)
    - [CoreBundle] fix cart-stock validation (https://github.com/coreshop/CoreShop/pull/894)
    - [Tests] [Behat] the cart tests haven't been ran since the theme-bundle was introduced (https://github.com/coreshop/CoreShop/pull/872)

## 2.0.x

### 2.0.12
- Translations:
    - New Crowdin translations (https://github.com/coreshop/CoreShop/pull/1165)
- Tests
    - [Tests] add conflict for monolog (https://github.com/coreshop/CoreShop/pull/1178)
- Bug
    - [TRACKING] use single item price in order item extractor (https://github.com/coreshop/CoreShop/pull/1231)

### 2.0.11
- Bugs:
    - [IndexBundle] use doctrine schema-config to create index-table schema (https://github.com/coreshop/CoreShop/pull/1142)

### 2.0.10
- Bugs:
    - [PayumBundle]: add sandbox to PayPal Configuration (https://github.com/coreshop/CoreShop/pull/1112)

### 2.0.9
- Bugs:
    - [Pimcore] fix stan tests on 2.0 (https://github.com/coreshop/CoreShop/pull/998)
    - [CurrencyBundle] fix for money-currency editable when data comes from pimcore editmode (https://github.com/coreshop/CoreShop/pull/1023)
    - [PimcoreBundle] use reflection to get class methods (https://github.com/coreshop/CoreShop/pull/1038)
    - [CoreExtensions] don't allow any diff until properly implemented (https://github.com/coreshop/CoreShop/pull/1050)
    - [ProductBundle] specific price rules: keep id on save (https://github.com/coreshop/CoreShop/pull/1045)
    - [TrackingBundle] fix total tax in tag manager tracker (https://github.com/coreshop/CoreShop/pull/1053) @solverat
    - [IndexBundle] fix iterator and nested interpreter (https://github.com/coreshop/CoreShop/pull/1054)
    - [IndexBundle] fix index iterator interpreter (https://github.com/coreshop/CoreShop/pull/1076)
    - [ResourceBundle] fix resource-select options (https://github.com/coreshop/CoreShop/pull/1077)
    - [Tests] fix stan and travis tests (https://github.com/coreshop/CoreShop/pull/1078)
    - [Docs] Fix extend docs (https://github.com/coreshop/CoreShop/pull/1082)
    - [PimcoreBundle] embedd CoreExtension - Protected members are available only via getters (https://github.com/coreshop/CoreShop/pull/1089) @rishadomar
    - [IndexBundle] fix order-direction now serialized properly (https://github.com/coreshop/CoreShop/pull/1097)
    - [All] Fixing Configuration Keys (https://github.com/coreshop/CoreShop/pull/1100) @khusseini
    - [FrontendBundle] CategoryController perPage Configuration fix (https://github.com/coreshop/CoreShop/pull/1105)

- Features:
    - [All] make CoreShop stores more async, don't load them initially (https://github.com/coreshop/CoreShop/pull/1025)
    - [Order] introduce checkout events (https://github.com/coreshop/CoreShop/pull/1043)
    - [CoreShop] Update CI (https://github.com/coreshop/CoreShop/pull/1056)
    - [CoreShop] Smaller CI changes (https://github.com/coreshop/CoreShop/pull/1060)

### 2.0.8
- Bug:
    - [Pimcore] Make CoreShop compatible with Pimcore 5.8.0 (https://github.com/coreshop/CoreShop/pull/977)
    - [Order] remove wrong type hints (https://github.com/coreshop/CoreShop/pull/978)
    - [Core] fix setting customer and persisting cart on user login (https://github.com/coreshop/CoreShop/pull/980)

### 2.0.7
- Bug:
    - [Taxation] Wrong Tax Calculation when using Store Gross Values (https://github.com/coreshop/CoreShop/issues/971)
    - [Core] remove unused Helper Classes (https://github.com/coreshop/CoreShop/pull/966)
    - [Core] add index to StorePrice Table for better performance (https://github.com/coreshop/CoreShop/pull/962)
    - [Core] remove result cache for Store Price due to issues with inheritance (https://github.com/coreshop/CoreShop/pull/961)
    - [Core] strip_tags for meta description in Category (https://github.com/coreshop/CoreShop/pull/959)
    - [Currency] MoneyCurrency fixes and improvements (https://github.com/coreshop/CoreShop/pull/958)
    - [Purchasable] When using custom purchasable, CoreShop always assumed that your class has a weight, even though it didn't had to. That's why we moved all weight related fields to the Core Component and CoreBundle.
    - [Core] prevent empty carts from being persisted (https://github.com/coreshop/CoreShop/issues/920)
    - [Core] move weight/total weight to Core Component (https://github.com/coreshop/CoreShop/pull/938)
    - [Address] use isoCode as fallback if no language value is set (https://github.com/coreshop/CoreShop/pull/939)

- Features
    - [JMS Serializer] Updated JMS Serializer to 2.0 (https://github.com/coreshop/CoreShop/pull/955)

### 2.0.6:
- Bug:
    - [Adjustments] in the classes "CoreShopOrderItem", "CoreShopQuoteItem", was a typo `basePdjustmentItems` instead of `baseAdjustmentItems`. Please manually rename them to `baseAdjustmentItems`
    - [ProductBundle] Fix extjs layout crash price rule is inactive (https://github.com/coreshop/CoreShop/pull/908)
    - [FrontendBundle] fix address creation redirect (https://github.com/coreshop/CoreShop/pull/910)
    - [StorageList] Storage List and Storage List Item is not a Pimcore Object (https://github.com/coreshop/CoreShop/pull/907)
    - [Order] fix typo in OrderItem and QuoteItem (https://github.com/coreshop/CoreShop/pull/906)
    - [NotificationBundle] fix typo in serializer namespace declaration (https://github.com/coreshop/CoreShop/pull/901)
    - [CoreBundle] fix notification rule setting for order mail action (https://github.com/coreshop/CoreShop/pull/886)
    - [Core] use PriceCalculatorInterface in product tracking extractor (https://github.com/coreshop/CoreShop/pull/892)
    - [Core] fix not passing full configuration in store based email (https://github.com/coreshop/CoreShop/pull/917)
    - [Core] fix copying object brick data from cart to sale (https://github.com/coreshop/CoreShop/pull/918)
    - [CoreBundle/OrderBundle] KernelResponse Event should ignore Profile Toolbar (https://github.com/coreshop/CoreShop/pull/919)
    - [Pimcore] Make CoreShop compatible with Pimcore 5.7.2 (https://github.com/coreshop/CoreShop/pull/914)

- Features:
    - [FrontendBundle] make private method protected (https://github.com/coreshop/CoreShop/pull/890)
    - [AddressBundle] introduce address-identifiers (https://github.com/coreshop/CoreShop/issues/830 & https://github.com/coreshop/CoreShop/pull/913)

### 2.0.5:
- Deprecations:
    - [WorkflowBundle] refactor state change logging (https://github.com/coreshop/CoreShop/pull/835)
        - `CoreShop\Bundle\OrderBundle\Workflow\OrderHistoryLogger` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\History\HistoryLoggerInterface` instead
        - `CoreShop\Bundle\OrderBundle\Workflow\OrderStateHistoryLogger` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\History\StateHistoryLoggerInterface` instead
        - `CoreShop\Bundle\OrderBundle\Workflow\WorkflowStateManager` has been deprecated, use `CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface` instead
- Bugs
    - [PimcoreBundle] Make embedded classes work for Pimcore 5.6 (https://github.com/coreshop/CoreShop/pull/867)
    - [All] Make CoreShop 2.0.x compatible with Pimcore 5.7 (https://github.com/coreshop/CoreShop/pull/869)
    - [All] fix PHP5 sort issues by removing them (https://github.com/coreshop/CoreShop/issues/840)
    - [Shipping] if cart has no shippables, don't force carriage calculation (https://github.com/coreshop/CoreShop/pull/863)
    - [Checkout] validate cart before submitting the order (https://github.com/coreshop/CoreShop/pull/858)
    - [Frontend] remove unused property in templates (https://github.com/coreshop/CoreShop/pull/859)
    - [Permissions] prefix permission labels (https://github.com/coreshop/CoreShop/pull/855)
    - [Order] refactor creation of invoices/shipments (https://github.com/coreshop/CoreShop/pull/852 + https://github.com/coreshop/CoreShop/pull/849)
    - [Order] introduce options for order-document-items (https://github.com/coreshop/CoreShop/pull/848)
    - [Autoloading] fix paths (https://github.com/coreshop/CoreShop/pull/846)
    - [ResourceBundle] Introduce more basic resource-types (https://github.com/coreshop/CoreShop/pull/838 + https://github.com/coreshop/CoreShop/pull/845)
    - [AddressBundle] add missing translations (https://github.com/coreshop/CoreShop/pull/836)

- Features
    - [Index] Introduce advanced sorting for indexes (https://github.com/coreshop/CoreShop/pull/856)

### 2.0.4
- Features:
    - [Reports] Reports Grid Sorting (https://github.com/coreshop/CoreShop/pull/828)
    - [Reports] Add Paginator To All Reports (https://github.com/coreshop/CoreShop/pull/826)
    - [Pimcore] introduce twig placeholder (https://github.com/coreshop/CoreShop/pull/827)
    - [Resource] add serialization for pimcore types (https://github.com/coreshop/CoreShop/pull/818)
    - [Resource] add more doctrine-pimcore-types (https://github.com/coreshop/CoreShop/pull/816)

- Bugs:
    - [Order] fix code-generator length (https://github.com/coreshop/CoreShop/pull/833)
    - [JS] Split JS Helpers into several regions of usability (https://github.com/coreshop/CoreShop/pull/829)
    - [Core] fix for cart-item digital product never set (https://github.com/coreshop/CoreShop/pull/821)
    - [Docs] Fix dead link (https://github.com/coreshop/CoreShop/pull/822)
    - [Resource] move settings icon to resource-bundle (https://github.com/coreshop/CoreShop/pull/815)
    - [Tracker] Update CompositeTracker::trackCheckoutStep (https://github.com/coreshop/CoreShop/pull/810)

### 2.0.3
- Features:
    - [All] Replace intval with int cast (https://github.com/coreshop/CoreShop/pull/805)
    - [Core] Store Price Dirty Detection if installed Pimcore Version >= 5.5 (https://github.com/coreshop/CoreShop/pull/807)
    - [Core] Allow Variants in Product Rule Conditions (https://github.com/coreshop/CoreShop/pull/794)
    - [Core] Add Event for Settings (https://github.com/coreshop/CoreShop/pull/785)
    - [Core] Extract CartItem Processor (https://github.com/coreshop/CoreShop/pull/784)
    - [Core] Decouple Shipping Calculator from Cart (https://github.com/coreshop/CoreShop/pull/783)
    - [Order] extract DataLoader into Pimcore Component (https://github.com/coreshop/CoreShop/pull/782)
    - [Order] rename Pimcore Grid Operators to be more CoreShop specific (https://github.com/coreshop/CoreShop/pull/787)
    - [Order] Check also for CurrencyAwareInterface in PriceFormatter Gird Operator (https://github.com/coreshop/CoreShop/pull/788)
    - [Index] Introduce Iterator Interpreter (https://github.com/coreshop/CoreShop/pull/802)
    - [Index] Introduce new Abstract function in AbstractWorker to allow handling of array data (https://github.com/coreshop/CoreShop/pull/803)
    - [Pimcore] add object_method twig function (https://github.com/coreshop/CoreShop/pull/809)
- Bugs:
    - [Core] Fix Gift Cart Price Rule Action (https://github.com/coreshop/CoreShop/pull/796)
    - [Core] Fix Invoice WKHTML Settings (https://github.com/coreshop/CoreShop/pull/786)
    - [Core] Rule Conditions: Check on type (https://github.com/coreshop/CoreShop/pull/779)
    - [Core] Add Translation for Adjustments (https://github.com/coreshop/CoreShop/pull/774)
    - [Pimcore] allow spaces in DynamicDropdown (https://github.com/coreshop/CoreShop/pull/781)

### 2.0.2
- Pimcore:
    - This release makes CoreShop compatible with Pimcore 5.6.0 (https://github.com/coreshop/CoreShop/pull/762)
- Features:
    - [Core] Adds a new CoreShop JS Event to add custom Menu Items to the CoreShop Menu (https://github.com/coreshop/CoreShop/pull/765)
    - [Resource] [ResourceBundle] add JMS Serializer Handler for Pimcore Objects (https://github.com/coreshop/CoreShop/pull/766)
- Bugs:
- [Tracking] Fixes a Bug in the Tracking Manager when a Product does not have any categories applied (https://github.com/coreshop/CoreShop/pull/767)

### 2.0.1
- Features:
    - [Core] Remove login customer after successfully registration (https://github.com/coreshop/CoreShop/pull/735)
- Bugs:
    - [Core] Countries are removed when removing Store (https://github.com/coreshop/CoreShop/pull/746)
    - [Core] order Document State Resolver when a Document is cancelled (https://github.com/coreshop/CoreShop/pull/738)
    - [Core] safe path for folders (https://github.com/coreshop/CoreShop/pull/742)
    - [Core] Fix for StoreMailActionProcessor exception in Notification Rule (https://github.com/coreshop/CoreShop/pull/740)
    - [Shipping] is invalid when no Shipping Rules are given (https://github.com/coreshop/CoreShop/pull/741)
    - [Frontend] Inaccurate Store Filter Query in Category Controller (https://github.com/coreshop/CoreShop/pull/744)

### 2.0.0
- CoreShop\Component\Index\Condition\RendererInterface has been deprecated in favor of CoreShop\Component\Index\Condition\DynamicRendererInterface to allow dynamic registration of condition renderers

### 2.0.0-RC.1
- Flash Messages are translated in the Controllers now, not in views anymore. If you have custom Flash Messages, translate them in your Controller instead of the view.

### 2.0.0-beta.4
- Completely remove FOSRestBundle, you still can use it, but you need to install it yourself. CoreShop only used the BodyListener to decode POST/PUT Requests, this Listener is now added by CoreShop if FOSRestBundle is not installed.

## 2.0.0-beta.3 to 2.0.0-beta.4
 - **BC break**: All occurrences of parameters `coreshop.all.stack.pimcore_class_ids`, `"application".model."class".pimcore_class_id`, `coreshop.all.pimcore_classes.ids` have been removed. Inject the corresponding Repository and use `classId` function instead
 - **Pimcore**: CoreShop now requires at least Pimcore 5.4.0. You need to update Pimcore to the at least 5.4.0 in order to use/update CoreShop.

### Product Price Calculation
In order to allow custom price calculation on API Level, we changed the way CoreShop calculates product prices by introducing a new parameter to every PriceCalculation Interface. Price Calculator Conditions are not anymore using a Live context, instead it gets passed via API.

Following interfaces have changed:

 - ```CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableDiscountCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableDiscountPriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface```
 - ```CoreShop\Component\Order\Calculator\PurchasableRetailPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductDiscountCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductDiscountPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface```
 - ```CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface```

If you have anything customized with those classes, please change them accordingly.

If you use the PriceCalculator Service directly, you also need to change the call from

```
$this->priceCalculator->getPrice($object, true)
```

to

```
$this->priceCalculator->getPrice($object, [
    'store' => $store,
    'country' => $country,
    'customer' => $customer,
    'currency' $currency,
    'cart' => $cart
], true)
```

The new second argument, is the Context for which you want to get the Price. This is highly depends on your
need. On a default CoreShop setup, the context can be fetched form the `ShopperContext` Service like this:

```
return  [
    'store' => $this->shopperContext->getStore(),
    'customer' => $this->shopperContext->hasCustomer() ? $this->shopperContext->getCustomer() : null,
    'currency' => $this->shopperContext->getCurrency(),
    'country' => $this->shopperContext->getCountry(),
    'cart' => $this->shopperContext->getCart()
];
```

CoreShop makes that easier for you, you can just use ```$this->shoppperContext->getContext()```. But be aware, the Shopper Context is only in a Web Request available.
When you work on a CLI Level, you have to define the context yourself.

If you already have a cart and want to calculate the price for the cart, the context is a bit different, cause it resolves from the cart

```
$context = [
    'store' => $cart->getStore(),
    'customer' => $cart->getCustomer() ?: null,
    'currency' => $cart->getCurrency(),
    'country' => $cart->getStore()->getBaseCountry(),
    'cart' => $cart
];
```

### Taxation
Tax Rule Store relation has been removed as it makes currently no sense.

### Tracking
Tracking bundle has completely been refactored in order to support more use-cases than just ecommerce. If you have any customizations made, please check the current implementation to adapt your changes.

### Order Invoice
Due to changes in adjustments, we also need to change OrderInvoice/OrderInvoiceItem.

After you have migrated to the latest version you also have to remove some fields:

#### CoreShopOrderInvoice
- totalTax
- baseTotalTax
- subtotalTax
- baseSubtotalTax
- shippingTax
- baseShippingTax
- taxes
- baseTaxes
- discountTax
- baseDiscountTax
- discountNet
- discountGross
- baseDiscountNet
- baseDiscountGross
- shippingNet
- shippingGross
- baseShippingNet
- baseShippingGross
- shippingTaxRate

#### CoreShopOrderInvoiceItem
- totalTax
- baseTotalTax
- taxes
- baseTaxes

### Adjustments
> **BC break / New Feature**

There are several deprecated class fields.
After you have migrated to the latest version you also have to remove them:

#### CoreShopOrder / CoreShopQuote
- shippingGross
- shippingNet
- shippingTax
- discountGross
- discountNet
- discountTax
- baseShippingNet
- baseShippingGross
- baseShippingTax
- baseDiscountNet
- baseDiscountGross
- totalTax
- baseTotalTax
- subTotalTax
- baseSubtotalTax

#### CoreShopOrderItem / CoreShopQuoteItem
- totalTax
- baseTotalTax

#### CoreShopCart
- shippingGross
- shippingNet
- discountGross
- discountNet

## 2.0.0-beta.2 to 2.0.0-beta.3
 - **BC break** Signature of following interfaces changed:
    - `CoreShop\Component\Index\Interpreter\InterpreterInterface`: public function interpret($value, IndexableInterface $object, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface`: public function interpretForLanguage($language, $value, IndexableInterface $object, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Index\Interpreter\RelationInterpreterInterface`: public function interpretRelational($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = []);
    - `CoreShop\Component\Customer\Model\UserInterface::ROLE_DEFAULT` renamed `CoreShop\Component\Customer\Model\UserInterface::CORESHOP_ROLE_DEFAULT`
    - `CoreShop\Component\Customer\Model\UserInterface::ROLE_SUPER_ADMIN` renamed `CoreShop\Component\Customer\Model\UserInterface::CORESHOP_ROLE_SUPER_ADMIN`

 - **BC break** Shipment / Invoice Creation via API changed
    - Before adding a new Shipment / Invoice you need to dispatch a request state to your order. Read more about it [here](./03_Development/06_Order/05_Invoice/01_Invoice_Creation.md) and [here](./03_Development/06_Order/06_Shipment/01_Shipment_Creation.md).
 - **BC break** getName in `CoreShop\Component\Index\Model\IndexableInterface` has been changed to `getIndexableName` as `getName` could eventually conflict with a non localized Pimcore Field
 - **BC break** getEnabled in `CoreShop\Component\Index\Model\IndexableInterface` has been changed to `getIndexableEnabled` as `getEnabled` could eventually conflict with a localized Pimcore Field

## 2.0.0-beta.1 to 2.0.0-beta.2
 - Link Generator implemented. If you want to use nice urls, you need to add the link generator service:
    - CoreShopProduct: add `@coreshop.object.link_generator.product` as Link Provider
    - CoreShopCategory: add `@coreshop.object.link_generator.category` as Link Provider
    - Change `{{ path('') }}` to `{{ coreshop_path('') }}`. You may want to checkout the FrontendBundle to get a deeper insight.
 - Deprecated Field Names in - CoreShop\Component\Payment\Model\PaymentInterface`:
    - getName is now getTitle
    - getOrderId is now getOrder and directly returns a OrderInterface
 - Deprecated Field Names in - `CoreShop\Component\Shipping\Model\CarrierInterface`:
    - getLabel is not getTitle
    - getName is now getIdentifier


## 2.0.0-alpha.4 to 2.0.0-beta.1
 - **BC break** Signature of following interfaces changed:
    - `CoreShop\Component\Index\Interpreter\InterpreterInterface`: public function interpret($value, IndexableInterface $object, IndexColumnInterface $config);
    - `CoreShop\Component\Index\Interpreter\LocalizedInterpreterInterface`: public function interpretForLanguage($language, $value, IndexableInterface $object, IndexColumnInterface $config);
    - `CoreShop\Component\Index\Interpreter\RelationInterpreterInterface`: public function interpretRelational($value, IndexableInterface $indexable, IndexColumnInterface $config);

 - **BC break** CoreShop now takes advantage of the dependent bundle feature introduced in Pimcore 5.1.2. Therefore,
 all bundles are now automatically loaded. This is a BC break, as when updating, you might run into issues.
 To solve the issues, check following things:
    - remove `RegisterBundles` entry from `app/AppKernel.php`
    - remove loading CoreShopCoreBundle Configuration in `app/config.yml`
    - enable CoreShopCoreBundle via CLI or manually in `var/config/extensions.php`: `"CoreShop\\Bundle\\CoreBundle\\CoreShopCoreBundle" => TRUE`
 - **BC break** Upgraded Default Layout to Bootstrap 4. This will most certainly cause issues when you just override certain templates. Best would be to copy all templates from before the Bootstrap 4 Upgrade.

## 2.0.0-alpha.4 to 2.0.0-alpha.5
 - **BC break** added Component\Core\Model\OrderItem and Component\Core\Model\QuoteItem. If you already customized them, inherit them from the Core Models.
 - **BC break** changed the way CoreShop processes Cart-Rules. If you implemented a custom-condition, inherit from `CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker` and implement `isCartRuleValid` instead of `isValid`
- Deprecated: remove `cart/add-price-rule/` static route.

## 2.0.0-alpha.3 to 2.0.0-alpha.4
 - **BC break** decoupled MoneyBundle from CurrencyBundle, therefore the Twig Extension for Money Conversion went to the CurrencyBundle. Therefore the name of that extension was renamed from
   **coreshop_convert_money** to **coreshop_convert_currency**. If you use it directly in your code, please rename all of them.
 - Deprecated CoreShopAdminBundle which responsability was only handling installation and deliviering pimcore resources like JS and CSS files.
    * Installation has been moved to CoreShopCoreBundle
    * Delivering of resources is now handled by CoreShopResourceBundle, this also makes it easier to use CoreShop Bundles without handling resources yourself.

## 2.0.0-alpha.2 to 2.0.0-alpha.3
 - **BC break** getPrice in PurchasableInterface and ProductInterface has been removed. In favor of this a new coreShopStorePrice editable has been introduced, which stores prices for each store. This makes handling of multiple currencies way more elegant.

   If you still want to use the old getPrice, create a new Subclass of \CoreShop\Component\Core\Model\Product, implement \CoreShop\Component\Order\Model\PriceAwarePurchasableInterface and set your class to CoreShopProduct parents.

# V1 to V2
 - CoreShop 2 is not backward compatible. Due to the framework change, we decided to re-make CoreShop from scratch. If you still have instances running and want to migrate, there is a basic migration way which gets you data from V1 to V2.
 - [Export from CoreShop1](https://github.com/coreshop/CoreShopExport)
 - [Import into CoreShop2](https://github.com/coreshop/ImportBundle)

# Within V1
 - Nothing available
