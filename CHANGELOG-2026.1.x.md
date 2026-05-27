## 2026.1.0

### Version scheme aligned with Pimcore

CoreShop switches from semantic versioning to Pimcore's year-based scheme. The previous line (`5.x`)
remains the last semver release and continues to receive maintenance; all new development targets
`2026.1` and up.

### Pimcore 2026.1 minimum requirement

All CoreShop components and bundles now require **`pimcore/pimcore: ^2026.1`**. Companion bundles
(`pimcore/studio-ui-bundle`, `pimcore/studio-backend-bundle`, `pimcore/generic-data-index-bundle`)
are likewise bumped to `^2026.1`.

### Classic Admin / ExtJS removed

Pimcore Classic Admin (ExtJS) support is removed. CoreShop 2026.1 targets **Pimcore Studio exclusively**.

Removed:

- All `Resources/public/pimcore/` ExtJS asset trees (~480 JS/CSS files across 22 bundles).
- All `AdminClass/` namespaces (grid column operators, admin-JS injection listeners, admin-grid filter
  listeners, the Pimcore Grid Config Installer).
- All `classic_admin.yml` service files and the conditional `PimcoreAdminBundle` loading blocks in
  bundle extensions.
- `pimcore_admin` configuration section from `CoreShopPimcoreExtension` and all bundle `Configuration`
  classes, plus the `registerPimcoreResources()` helper on `AbstractPimcoreExtension`.
- `CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface`, `AsGridFilter` attribute,
  `RegisterGridFilterPass` compiler pass, `GridConfigInstaller` / `GridConfigInstallerInterface`.
- `CoreShop\Bundle\PimcoreBundle\Controller\Admin\*` controllers. Studio-facing actions were moved to
  `CoreShop\Bundle\PimcoreBundle\Controller\{DynamicDropdown,Grid}Controller`.
- ExtJS menu rendering (`coreshop_menu` route, `menu.js.twig`, `JsonRenderer`). The Studio menu API
  (`coreshop_menu_api`, `StudioRenderer`) is unchanged.
- `pimcore/admin-ui-classic-bundle` suggest entries from all bundle `composer.json`.
- Classic Admin firewall / ACL / role hierarchy scaffolding in `config/packages/security.yaml`.

If you have project code referencing any of these symbols, see the migration notes in the
upgrade guide.

### Studio extension points unchanged

The following Studio APIs, routes, and registries continue to work as in `5.1`:

- `GET /pimcore-studio/api/coreshop/grid/studio-filters/{listType}`
- `GET /pimcore-studio/api/coreshop/grid/actions/{listType}`
- `POST /pimcore-studio/api/coreshop/grid/apply-action`
- `/pimcore-studio/api/coreshop/dynamic-dropdown/{options,methods}`
- `/pimcore-studio/api/coreshop/menus`
- The seven entity extension types (form / table-column / save-decorator / tab / action / validation /
  lifecycle) and all dynamic type registrations.

### Static routes migrated to native Symfony routing (opt-in)

CoreShop no longer depends on the deprecated `pimcore/static-routes-bundle`. The ~40 frontend shop
routes previously defined in `FrontendBundle/Resources/install/pimcore/staticroutes.yml` and written
into Pimcore's `settings-store` on install are now declared as native Symfony routes under
`FrontendBundle/Resources/config/routes/`, split per URL topic (`shop/index.yaml`,
`shop/cart.yaml`, `shop/catalog.yaml`, `shop/checkout.yaml`, `shop/customer.yaml`, `shop/wishlist.yaml`,
`partial.yaml`).

**Routes are opt-in.** The bundle no longer auto-registers them via Pimcore's `pimcore_bundle` route
loader. Projects must explicitly import them in their own `config/routes.yaml`:

```yaml
coreshop_frontend:
    resource: "@CoreShopFrontendBundle/Resources/config/routes.yaml"
```

Projects with a custom storefront can skip the top-level import and cherry-pick only the sub-files
they want (e.g. `routes/shop/cart.yaml` without `routes/shop/checkout.yaml`). See the Installation
Guide for details.

Additional effects:

- Installing CoreShop no longer runs a route-installation step; the Symfony router picks the routes
  up at cache warm-up.
- `PimcoreRoutesInstaller`, `RouteConfiguration`, and the `coreshop.resource.installer.routes`
  service definition are removed.
- `PimcoreStaticRoutesBundle` is removed from `config/bundles.php` and from the conditional
  registration in `CoreShopResourceBundle::registerDependentBundles()`.
- `LocaleSwitcherExtension` no longer calls `Staticroute::getByName()` — it inspects the current
  route's compiled parameters via `RouterInterface::getRouteCollection()`.
- The `pimcore_wishlist_summary` path previously exposed both `/{_locale}/shop/wishlist` and
  `/{_locale}/shop/wishlist/{identifier}` under one name. The identifier variant is now a separate
  route, `coreshop_wishlist_summary_identifier`. Existing twig/template callers pass no identifier
  and are unaffected.
- Route names and reverse URLs are preserved 1:1 — no template or redirect URL change is required.

### Documentation

Developer docs covering `rule-engine`, `payment-provider`, `menu-bundle`, `form-extension`, and
`order-detail` extension have been rewritten to use Studio patterns (StudioFormBundle schema,
TabExtension / ActionExtension slots). ExtJS code samples were removed.
