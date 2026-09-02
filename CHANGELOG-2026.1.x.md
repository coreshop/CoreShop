## 2026.1.0

### Version scheme aligned with Pimcore

CoreShop switches from semantic versioning to Pimcore's year-based scheme. The previous line (`5.x`)
remains the last semver release and continues to receive maintenance; all new development targets
`2026.1` and up.

### Pimcore 2026.2 minimum requirement

All CoreShop components and bundles now require **`pimcore/pimcore: ^2026.2`**. Companion bundles
(`pimcore/studio-ui-bundle`, `pimcore/studio-backend-bundle`, `pimcore/generic-data-index-bundle`,
`pimcore/opensearch-client`) are likewise bumped to `^2026.2`.

The floor tracks the Pimcore Studio SDK that CoreShop's Studio plugins are built and tested against.
Pimcore removes exported symbols between minor versions, so declaring a minimum older than the SDK we
build against would let Composer install a Pimcore that cannot load the shipped Studio bundles.

### Classic Admin / ExtJS removed

Pimcore Classic Admin (ExtJS) support is removed. CoreShop 2026.1 targets **Pimcore Studio exclusively**.

Removed:

- All `Resources/public/pimcore/` ExtJS asset trees (~480 JS/CSS files across 22 bundles).
- All `AdminClass/` namespaces (grid column operators, admin-JS injection listeners, admin-grid filter
  listeners, the Pimcore Grid Config Installer).
- All `classic_admin.yml` service files and the conditional `PimcoreAdminBundle` loading blocks in
  bundle extensions.
- The classic-admin asset keys of the `pimcore_admin` configuration section (`js`, `css`, `editmode_js`,
  `editmode_css`), along with the installers behind the `grid_config` and `routes` install types. The
  section itself and the `registerPimcoreResources()` helper on `AbstractPimcoreExtension` remain, and
  still carry the backend-relevant `permissions` and `install` declarations — see "Backend declarations
  kept" below.
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

### Backend declarations kept: `pimcore_admin` and `registerPimcoreResources()`

Despite its name, the `pimcore_admin` configuration section was never only about the classic admin. Besides
the ExtJS asset keys it carries the declarations that drive the resource installers, and those are unrelated
to the admin UI:

- `permissions` — the Pimcore user-permission definitions installed by `PimcorePermissionInstaller`.
- `install.documents` / `install.image_thumbnails` / `install.sql` — installed by
  `PimcoreDocumentsInstaller`, `PimcoreImageThumbnailsInstaller` and `SqlInstaller`.

The section and the `registerPimcoreResources()` helper therefore stay. Only the classic-admin keys are gone,
and they are *ignored* rather than rejected, so a bundle that still declares `js` / `css` alongside its
`permissions` keeps working on 2026.x with no changes to its `Configuration` class or its extension.

The section name is kept as `pimcore_admin` for backwards compatibility, even though it is now a purely
historical name. This is deliberate and permanent: there is no rename, no alias and no deprecation, because
bundles outside the core declare `pimcore_admin: { permissions: [...] }` and call `registerPimcoreResources()`
verbatim and must keep working. The same goes for the method name.

See `docs/03_Bundles/Resource_Bundle/06_User_Permissions.md`.

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

### Configurable translation `locale` column length

`ORMTranslatableListener` hardcoded the Doctrine ORM mapping for every `*_translation` entity's
`locale` column to `length: 5`. This is too short for any locale identifier beyond a plain
`language_REGION` code.

A [BCP 47 / RFC 5646](https://www.rfc-editor.org/rfc/rfc5646.html) language tag (Pimcore/ICU locale
strings use `_` instead of `-`, but the subtag rules are the same) is built as:

```
language ["-" script] ["-" region] *("-" variant)
```

- `language`: 2–3 letters (rarely up to 8 for registered/reserved tags)
- `script`: **exactly 4 letters** (ISO 15924), e.g. `Hans`, `Hant`, `Cyrl`, `Latn`
- `region`: 2 letters (ISO 3166-1) or 3 digits (UN M49)
- `variant`: 5–8 alphanumeric chars, or 1 digit + 3 alphanumeric chars, and — per the `*("-" variant)`
  grammar in [RFC 5646 §2.2.5](https://www.rfc-editor.org/rfc/rfc5646.html#section-2.2.5) — a tag can
  carry **any number of them**. There is no fixed maximum tag length; `5` (or any other single value)
  is only ever a practical choice for the locales a given project actually uses, never a technically
  correct upper bound.

Real-world examples already longer than 5 characters:

- `zh_Hans` / `zh_Hant` — 7 chars (script only, no region)
- `zh_Hant_HK`, `zh_Hant_TW`, `zh_Hans_SG` — 10 chars (script + region)
- `sr_Cyrl_RS` / `sr_Latn_RS` — 10 chars (Serbian, Cyrillic vs. Latin, Serbia)
- `uz_Cyrl_UZ` / `uz_Latn_UZ` — 10 chars (Uzbek)
- `pa_Arab_PK` / `pa_Guru_IN` — 10 chars (Punjabi, Shahmukhi vs. Gurmukhi script)
- `de_DE_1901` — 10 chars (German, traditional 1901 orthography *variant*, not a script)

All of these are real ICU/CLDR locale IDs — the same data source Symfony's
[Intl component](https://symfony.com/doc/current/components/intl.html) (and by extension Pimcore's
locale list) draws from, and the identifier grammar itself comes from
[Unicode UTS #35](https://www.unicode.org/reports/tr35/#Identifiers). `zh_Hans` / `zh_Hant` are only
the example that was hit in practice — MySQL silently truncated them to `zh_Ha`, which then collided
with the `(translatable_id, locale)` unique constraint and caused a duplicate-key error on insert.

The column length is now configurable:

```yaml
core_shop_resource:
    translation:
        locale_column_length: 8
```

**The default stays `5`** — this is opt-in. If you raise it, you are responsible for a migration:
changing this value only updates Doctrine's *mapping metadata* for newly created schemas. It does
**not** alter any already-created database column. You must ship your own
`ALTER TABLE ... MODIFY locale VARCHAR(<n>)` migration for every existing `*_translation` table, or
inserts will keep failing/truncating against the old column width.
