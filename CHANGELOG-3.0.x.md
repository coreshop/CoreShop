# 3.0.7

## Bugs
 - [CartPriceRule] fix issue with deleted price rule (https://github.com/coreshop/CoreShop/pull/2332)

# 3.0.6

## Bugs
 - [StorageList] add default session based context resolver (https://github.com/coreshop/CoreShop/pull/2269)
 - [Pimcore] Handle unknown class names in ClassUpdate (https://github.com/coreshop/CoreShop/pull/2271)
 - [Guzzle] use ^1.0 for guzzle adapter (https://github.com/coreshop/CoreShop/pull/2274)
 - [FrontendBundle] throw 404 when Reset-Token not found (https://github.com/coreshop/CoreShop/pull/2277)
 - [IndexBundle] support 'zero' values for filtering (https://github.com/coreshop/CoreShop/pull/2282)
 - [Pimcore] make compatible with Pimcore 10.6 (https://github.com/coreshop/CoreShop/pull/2285)
 - [Order] fix voucher modifier calling findByCode with null (https://github.com/coreshop/CoreShop/pull/2289)
 - [MenuBundle] fix Pimcore 10.6 compatibility issue (https://github.com/coreshop/CoreShop/pull/2292)
 - [ResourceBundle] fix Pimcore 10.6 compatibility to find Admin User (https://github.com/coreshop/CoreShop/pull/2293)
 - [OrderBundle] improve unlinking files in pdf generation (https://github.com/coreshop/CoreShop/pull/2294)
 - [SerializedData] show data as string in version preview (https://github.com/coreshop/CoreShop/pull/2295)
 - [Docs] Maintenance mode <-> Maintenance job (https://github.com/coreshop/CoreShop/pull/2299)
 - [MessengerBundle] fix for standalone installation (https://github.com/coreshop/CoreShop/pull/2298)

# 3.0.5

## Bugs
 - [CoreBundle] fix column names for coreshop_carrier_store table (https://github.com/coreshop/CoreShop/pull/2187)
 - [PHP] Require at least php 8 via composer.json (https://github.com/coreshop/CoreShop/pull/2210)
 - [Store] Throw exception if site store isnt found in SiteBasedRequestResolver (https://github.com/coreshop/CoreShop/pull/2209)
 - [FrontendBundle] Checkout allow to pass query params from step to step (https://github.com/coreshop/CoreShop/pull/2224)
 - [ResourceBundle] fix: added event.js on ResourceBundle (https://github.com/coreshop/CoreShop/pull/2228)
 - [FrontendBundle] restrict parentCategoryIds in filter index (https://github.com/coreshop/CoreShop/pull/2230)
 - [Variant] fix sorting in findMainVariant (https://github.com/coreshop/CoreShop/pull/2232)
 - [StorageList] fix missing classes (https://github.com/coreshop/CoreShop/pull/2235)
 - [ResourceBundle] re-implement expression evaluation (https://github.com/coreshop/CoreShop/pull/2237)
 - [CoreBundle] add missing parameter identifier (https://github.com/coreshop/CoreShop/pull/2241)
 - [StorageListBundle] fix loading 'coreshop.context.cart' (https://github.com/coreshop/CoreShop/pull/2246)
 - [SEO] Check first if metatitle is not null before setting it SEO TitleExtractor (https://github.com/coreshop/CoreShop/pull/2242)
 - [Services] Fix deprecation format (https://github.com/coreshop/CoreShop/pull/2244)
 - [Shipping] don't include shipping total in amount shipping condition (https://github.com/coreshop/CoreShop/pull/2238)
 - [Slugs] Make URL slugs overwritable (https://github.com/coreshop/CoreShop/pull/2236)
 - [Core] Use invoice address of logged in customer for tax calculation (https://github.com/coreshop/CoreShop/pull/2254)
 - [Order] don't add CartItemPriceRule if not applicable (https://github.com/coreshop/CoreShop/pull/2258)
 - [Cache] disabling caching of StorageLists for better concurrency (https://github.com/coreshop/CoreShop/pull/2250)
 - [Docs] Update docs (https://github.com/coreshop/CoreShop/pull/2259)
 - [Pimcore] add locale to SlugGenerationEvent (https://github.com/coreshop/CoreShop/pull/2260)
 - [Core] Support "store" field type in grid (https://github.com/coreshop/CoreShop/pull/2262)
 - [WishlistBundle] make wishlist easier usable outside of CoreShop (https://github.com/coreshop/CoreShop/pull/2266)
 - [FrontendBundle] Adjusted locale switcher for internal shop pages without translations (https://github.com/coreshop/CoreShop/pull/2261)
 
# 3.0.4

## Bugs
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

# 3.0.3

## Feature
 - [StorageList] allow for shareable StorageLists (eg. wishlist) (https://github.com/coreshop/CoreShop/pull/2150)

## Bugs
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

# 3.0.2

> Please make sure you also start the messenger worker for the CoreShop Tasks
> ```bin/console messenger:consume coreshop_notification coreshop_index --time-limit=300```

## Features
 - [Messenger] introduce Messenger Bundle (https://github.com/coreshop/CoreShop/pull/2105, https://github.com/coreshop/CoreShop/pull/2114, https://github.com/coreshop/CoreShop/pull/2112, https://github.com/coreshop/CoreShop/pull/2106)

## Bugs
 - [CartPiceRules] fix OrderItem not finding Order with CartItemPriceRules (https://github.com/coreshop/CoreShop/pull/2116)
 - [StorageList] fix storage-list priority (https://github.com/coreshop/CoreShop/pull/2113)
 - [ResourceBundle] class names with lower-case names (https://github.com/coreshop/CoreShop/pull/2097)
 - [Checkout] introduce Payment Provider Validator to check if the selected one is still valid (https://github.com/coreshop/CoreShop/pull/2111)
 - [Unit] fix issues with unit selection in ui (https://github.com/coreshop/CoreShop/pull/2104)
 - [CustomerAssignment] Fix typo when passing the company ID to the router (https://github.com/coreshop/CoreShop/pull/2102)
 - [CoreBundle] fix cart-item-rule discount action form (https://github.com/coreshop/CoreShop/pull/2101)
 - [IndexBundle] fix: index table o_classId type fix (https://github.com/coreshop/CoreShop/pull/2095)
 - [Menu] Use correct permission definition for product unit menu item (https://github.com/coreshop/CoreShop/pull/2094)

# 3.0.1

## Bugs
 - [Translations] fix order object translations (https://github.com/coreshop/CoreShop/pull/2091)
 - [Psalm] fixes (https://github.com/coreshop/CoreShop/pull/2089)
 - [CustomerTransformHelper] Use company's name initial as parent folder for companies (https://github.com/coreshop/CoreShop/pull/2082)
 - [StorageListBundle] PimcoreStorageListRepository: Comply to PSR-4 autoloading standards (https://github.com/coreshop/CoreShop/pull/2081)
 - [ProductVariantTrait] Prevent MySQL syntax error (https://github.com/coreshop/CoreShop/pull/2086)
 - [Wishlist] add tests and fix routing (https://github.com/coreshop/CoreShop/pull/2084) 

# 3.0.0

CoreShop is now Licenced under CCL and GPLv3! (https://github.com/coreshop/CoreShop/pull/2061)

## Feature
 - [IndexBundle] clone index, change default name of cloned item (https://github.com/coreshop/CoreShop/pull/2056)
 - [CartPriceRules] introduce feature to allow cart-price rules based on cart-items (https://github.com/coreshop/CoreShop/pull/2057, https://github.com/coreshop/CoreShop/pull/2060)
 - [Wishlist] Introduce a persisted wishlist - StorageListBundle now works as a base for Order and Wishlist (https://github.com/coreshop/CoreShop/pull/2030, https://github.com/coreshop/CoreShop/pull/2066)
 - [Reports] Support filtering for order type (https://github.com/coreshop/CoreShop/pull/2055)
 - [Symfony] fix Injecting @session is deprecated with Symfony (https://github.com/coreshop/CoreShop/pull/2035)
 - [AccessManagement] prepare CoreShop for advanced access-management (https://github.com/coreshop/CoreShop/pull/2063)
 - [Pimcore] 10.5 as min requirement (https://github.com/coreshop/CoreShop/pull/2067)

## Bugs
 - [VariantBundle] Serializer: Allow $innerObject to be null (https://github.com/coreshop/CoreShop/pull/2058, https://github.com/coreshop/CoreShop/pull/2069)
 - [DataHub] Fix non unique typename (https://github.com/coreshop/CoreShop/pull/2004)
 - [Translations] Update admin-translations.yml (https://github.com/coreshop/CoreShop/pull/2064)
 - [Pimcore UI] Make default Product Unit unselectable (https://github.com/coreshop/CoreShop/pull/2065)
 - [Variant] allow recursive attributes and variants (https://github.com/coreshop/CoreShop/pull/2068)

# 3.0.0-beta.5
 > This will be the last BETA for the final release.

## Bugs
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

# 3.0.0-beta.4
## Feature
 - [Variants] introduce Variant Bundle (https://github.com/coreshop/CoreShop/pull/1990) @breakone
 - [Pimcore] require min 10.4 (https://github.com/coreshop/CoreShop/pull/2013)

## Bugs
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
 - 

# 3.0.0-beta.3
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


# 3.0.0-beta.2

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

# 3.0.0-beta.1

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
