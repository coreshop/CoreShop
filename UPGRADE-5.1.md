# Upgrade to 5.1

## Classic-Admin internals moved to `AdminClass/` subnamespace

Helper classes that exist solely to integrate with the ExtJS-based Classic Admin
have been moved into a new `AdminClass/` subnamespace inside their bundles. This
isolates Classic-Admin-only code so it can be cleanly removed in a future major
version once Pimcore Studio v2 covers all functionality.

The classes are unchanged in behavior — only their fully-qualified namespace is
different. Classic Admin continues to work without any user action required.

You only need to take action if you directly imported one of these classes via
`use` statements, referenced them by FQCN as a service ID in your own
configuration, or extended them in a custom subclass.

### Renamed classes

| Old FQCN | New FQCN |
|---|---|
| `CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\PriceFormatter` | `CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\PriceFormatter` |
| `CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\OrderState` | `CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\OrderState` |
| `CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory\OrderStateFactory` | `CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\Factory\OrderStateFactory` |
| `CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator\Factory\PriceFormatterFactory` | `CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\Operator\Factory\PriceFormatterFactory` |
| `CoreShop\Bundle\OrderBundle\EventListener\Grid\CartFilterListener` | `CoreShop\Bundle\OrderBundle\AdminClass\EventListener\Grid\CartFilterListener` |
| `CoreShop\Bundle\ResourceBundle\Pimcore\GridColumnConfig\ResourceFieldGetter` | `CoreShop\Bundle\ResourceBundle\AdminClass\Pimcore\GridColumnConfig\ResourceFieldGetter` |
| `CoreShop\Bundle\ResourceBundle\Installer\PimcoreGridConfigInstaller` | `CoreShop\Bundle\ResourceBundle\AdminClass\Installer\PimcoreGridConfigInstaller` |
| `CoreShop\Bundle\MoneyBundle\EventListener\IndexActionSettingsSubscriber` | `CoreShop\Bundle\MoneyBundle\AdminClass\EventListener\IndexActionSettingsSubscriber` |
| `CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\StorePrice` | `CoreShop\Bundle\CoreBundle\AdminClass\Pimcore\GridColumnConfig\Operator\StorePrice` |
| `CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\Factory\StorePriceFactory` | `CoreShop\Bundle\CoreBundle\AdminClass\Pimcore\GridColumnConfig\Operator\Factory\StorePriceFactory` |
| `CoreShop\Bundle\CoreBundle\EventListener\ProductStoreValuesAdminGetListener` | `CoreShop\Bundle\CoreBundle\AdminClass\EventListener\ProductStoreValuesAdminGetListener` |
| `CoreShop\Bundle\CoreBundle\EventListener\CustomerOrderDeletionListener` | `CoreShop\Bundle\CoreBundle\AdminClass\EventListener\CustomerOrderDeletionListener` |
| `CoreShop\Bundle\CoreBundle\EventListener\AdminJavascriptListener` | `CoreShop\Bundle\CoreBundle\AdminClass\EventListener\AdminJavascriptListener` |
| `CoreShop\Bundle\MenuBundle\EventListener\MenuAdminListener` | `CoreShop\Bundle\MenuBundle\AdminClass\EventListener\MenuAdminListener` |
| `CoreShop\Bundle\MenuBundle\EventListener\PimcoreAdminListener` | `CoreShop\Bundle\MenuBundle\AdminClass\EventListener\PimcoreAdminListener` |
| `CoreShop\Bundle\PimcoreBundle\EventListener\AdminJavascriptListener` | `CoreShop\Bundle\PimcoreBundle\AdminClass\EventListener\AdminJavascriptListener` |
| `CoreShop\Bundle\PimcoreBundle\EventListener\Grid\ObjectListFilterListener` | `CoreShop\Bundle\PimcoreBundle\AdminClass\EventListener\Grid\ObjectListFilterListener` |

### How to fix

Search-and-replace your codebase / config:

```
CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\
  → CoreShop\Bundle\OrderBundle\AdminClass\Pimcore\GridColumnConfig\
CoreShop\Bundle\OrderBundle\EventListener\Grid\
  → CoreShop\Bundle\OrderBundle\AdminClass\EventListener\Grid\
CoreShop\Bundle\ResourceBundle\Pimcore\GridColumnConfig\
  → CoreShop\Bundle\ResourceBundle\AdminClass\Pimcore\GridColumnConfig\
CoreShop\Bundle\ResourceBundle\Installer\PimcoreGridConfigInstaller
  → CoreShop\Bundle\ResourceBundle\AdminClass\Installer\PimcoreGridConfigInstaller
CoreShop\Bundle\MoneyBundle\EventListener\IndexActionSettingsSubscriber
  → CoreShop\Bundle\MoneyBundle\AdminClass\EventListener\IndexActionSettingsSubscriber
CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\
  → CoreShop\Bundle\CoreBundle\AdminClass\Pimcore\GridColumnConfig\
CoreShop\Bundle\CoreBundle\EventListener\(ProductStoreValuesAdminGetListener|CustomerOrderDeletionListener|AdminJavascriptListener)
  → CoreShop\Bundle\CoreBundle\AdminClass\EventListener\$1
CoreShop\Bundle\MenuBundle\EventListener\
  → CoreShop\Bundle\MenuBundle\AdminClass\EventListener\
CoreShop\Bundle\PimcoreBundle\EventListener\
  → CoreShop\Bundle\PimcoreBundle\AdminClass\EventListener\
```

If you used the recommended symbolic service IDs (e.g. `coreshop.order.grid.price_formatter`)
instead of FQCN-as-service-ID, no DI changes are needed.

## Admin controllers no longer extend `Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController`

CoreShop's admin controllers no longer extend Pimcore's `AdminAbstractController`,
which has been deprecated upstream. Methods previously inherited from it
(`adminJson`, `getAdminUser`, `pimcoreSerializer`, etc.) are no longer available
on these controllers.

This affects you only if you wrote a custom subclass of one of CoreShop's admin
controllers and called any of those inherited methods. Inject the equivalent
services directly in your subclass instead.

## Studio builds ship as archives

The compiled Studio frontend of each bundle is no longer committed as an expanded
`Resources/public/studio/<id>/` directory. Each bundle ships a single archive
`Resources/build-dist/build-<id>.zip` instead, which Pimcore's `BuildArchiveExtractor`
(pimcore/studio-ui-bundle ≥ 2025.4.6) unpacks into `Resources/public/studio/` during cache
warmup. This follows the model Pimcore introduced for its own Studio UI build.

Deployments on a read-only filesystem must run `bin/console cache:warmup` (or `cache:clear`)
during the build or deploy phase while `vendor/` is still writable. Standard Pimcore
deployments already do this. When `assets:install` runs in copy mode (without `--symlink`),
run `cache:warmup` before it so the extracted build gets copied.
