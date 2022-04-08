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
