# 3.0

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
