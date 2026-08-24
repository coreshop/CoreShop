# Installation

To install CoreShop, ensure you have a running instance of Pimcore on your system. Follow these steps to set up
CoreShop:

## Initial Setup

1. Install with composer: `composer require coreshop/core-shop ^2026.0`
2. Enable the bundle in `config/bundles.php` and the `CoreShopCoreBundle` to the list of Bundles to load:
    ```php
    <?php
    
    return [
       ...
        CoreShop\Bundle\CoreBundle\CoreShopCoreBundle::class => ['all' => true],
        CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle::class => ['all' => true],
    ];
    ```
3. To configure the CoreShop Password Hasher, create or update the `config/packages/pimcore.yaml` and add:
      ```yaml
      pimcore:
          security:
              password_hasher_factories:
                  Pimcore\Model\DataObject\CoreShopUser: coreshop.security.user.password_hasher_factories
      ```
4. Update `config/packages/security.yaml` to allow access to the CoreShop Backend.
    - Add the CoreShop Frontend parameter at the very top of your `security.yaml` (before `security`):
      ```yaml
      parameters:
          coreshop.security.frontend_regex: "^/(?!admin|pimcore-studio)[^/]*"

      security:
          ...
      ```
    - Add the Authentication Provider. Find your existing `providers` entry and add the `coreshop_user` entry after the other(s):
      ```yaml
       providers:
         ...
         coreshop_user:
             id: CoreShop\Bundle\CoreBundle\Security\ObjectUserProvider
      ```
    - Add the Firewall Config. Find your existing `firewalls` entry and add the `coreshop_frontend` entry after the other
      entries - in particular after `pimcore_studio`, since Symfony uses the first firewall whose pattern matches:
      ```yaml
       firewalls:
         coreshop_frontend:
             provider: coreshop_user
             pattern: '%coreshop.security.frontend_regex%'
             context: shop
             form_login:
                 login_path: coreshop_login
                 check_path: coreshop_login_check
                 provider: coreshop_user
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
      ```
    - Add Access Control. Find your existing `access_control` entry and add the following lines after the existing ones:
      ```yaml
       access_control:
         - { path: "%coreshop.security.frontend_regex%/_partial", role: IS_AUTHENTICATED_ANONYMOUSLY, ips: [127.0.0.1, ::1] }
         - { path: "%coreshop.security.frontend_regex%/_partial", role: ROLE_NO_ACCESS }
      ```
5. **Activate Frontend Routes (opt-in).**
   CoreShop's `FrontendBundle` does **not** auto-register its storefront URLs — you decide whether your project wants
   them. Add the import to `config/routes.yaml`:
    ```yaml
    # config/routes.yaml
    _pimcore:
        resource: "@PimcoreCoreBundle/config/routing.yaml"

    coreshop_frontend:
        resource: "@CoreShopFrontendBundle/Resources/config/routes.yaml"
    ```
   This loads the full route tree:
    - `coreshop_payment_after` (`/cs/after-pay`) — Payum return URL
    - `coreshop_frontend_shop` — ~35 locale-prefixed shop routes under `/{_locale}/shop/…` (index, cart, catalog,
      checkout, customer, wishlist)
    - `coreshop_frontend_partial` — ESI/ajax fragments under `/{_locale}/_partial/…`

   **Need only a subset?** Import the sub-files directly instead of the top-level file. Example — a headless project
   that still needs the payment callback but no storefront:
    ```yaml
    # config/routes.yaml
    coreshop_payment_after:
        path: /cs/after-pay
        methods: [GET, POST]
        defaults:
            _controller: CoreShop\Bundle\PayumBundle\Controller\PaymentController::afterCaptureAction
    ```
   Example — shop with custom checkout pages (skip CoreShop's `/shop/checkout/*`):
    ```yaml
    coreshop_frontend_shop_index:
        resource: "@CoreShopFrontendBundle/Resources/config/routes/shop/index.yaml"
        prefix:   /{_locale}/shop
        requirements: { _locale: '[a-z]{2}(_[A-Z]{2})?' }

    coreshop_frontend_shop_cart:
        resource: "@CoreShopFrontendBundle/Resources/config/routes/shop/cart.yaml"
        prefix:   /{_locale}/shop
        requirements: { _locale: '[a-z]{2}(_[A-Z]{2})?' }

    # …skipped: shop/checkout.yaml — your project defines its own
    ```
6. Install Pimcore using the CoreShop install profile — sets up the database only. CoreShop's own
   classes, permissions, and fixtures follow in step 7.
    ```bash
    vendor/bin/pimcore-install \
        --install-profile 'CoreShop\Bundle\CoreBundle\InstallProfile\CoreShopInstallProfile' \
        --skip-validation \
        --no-interaction \
        --admin-username admin \
        --admin-password admin
    ```
    The profile expects the following env vars (either in `.env.local` or exported):
    - `DATABASE_URL` — Doctrine DBAL DSN, e.g. `mysql://user:pass@host:3306/dbname`
    - `PIMCORE_OPENSEARCH_DSN` — OpenSearch DSN, e.g. `opensearch://admin@localhost:9200?ssl=true&ssl_verify=false`
    - `PIMCORE_MESSENGER_TRANSPORT_DSN_PREFIX` — auto-filled by the profile to `doctrine://default?queue_name=`
    - `PIMCORE_ADMIN_USER` / `PIMCORE_ADMIN_PASSWORD` — or pass `--admin-username` / `--admin-password` on the CLI as shown

    See `CoreShop\Bundle\CoreBundle\InstallProfile\CoreShopInstallProfile` for the env var
    definitions it registers. All CoreShop-specific install steps (Pimcore classes, permissions,
    documents, fixtures) run in the next step.
7. Run CoreShop install: `php bin/console coreshop:install`
8. Optional: Install Demo Data `php bin/console coreshop:install:demo`

## Messenger

CoreShop also uses Symfony Messenger for async tasks like sending E-Mails or Processing DataObjects for the Index.
Please run these 2 transports to process the data

```yaml
bin/console messenger:consume coreshop_notification coreshop_index coreshop_variant --time-limit=300
```

## Payment

CoreShop uses Payum for Payment. Checkout Payum's Documentation on how to add payment providers.

Payment providers are implemented as Pimcore Plugin. They can be installed using composer. Here you can find all
available payment modules via composer

[Payum Documentation](https://payum.gitbook.io/payum)
