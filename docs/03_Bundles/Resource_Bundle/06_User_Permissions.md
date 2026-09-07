# User Permissions

CoreShop gates menu entries, Studio widgets and controllers behind Pimcore user permissions, for example
`coreshop_permission_order_list`. A permission can only be granted to a Pimcore user or role once a
`Pimcore\Model\User\Permission\Definition` row exists for it, so every permission a bundle checks has to be
*declared* first. `coreshop:resources:install` then writes the declarations into Pimcore.

## Declaring permissions

Declare them in the `pimcore_admin` section of your bundle's configuration and register that section from your
DI extension. This is the same mechanism CoreShop has used since 4.x — it is unchanged on 2026.x.

```php
<?php
// YourBundle/DependencyInjection/Configuration.php

private function addPimcoreResourcesSection(ArrayNodeDefinition $node): void
{
    $node->children()
        ->arrayNode('pimcore_admin')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('permissions')
                    ->cannotBeOverwritten()
                    ->defaultValue(['dashboard', 'settings'])
                ->end()
            ->end()
        ->end()
    ->end()
    ;
}
```

```php
<?php
// YourBundle/DependencyInjection/CoreShopYourBundleExtension.php

public function load(array $configs, ContainerBuilder $container): void
{
    // ...

    if (array_key_exists('pimcore_admin', $configs)) {
        $this->registerPimcoreResources('coreshop_your_bundle', $configs['pimcore_admin'], $container);
    }
}
```

The first argument to `registerPimcoreResources()` is your application name, and every permission entry is
prefixed with it. The example above declares `coreshop_your_bundle_permission_dashboard` and
`coreshop_your_bundle_permission_settings`, both installed into the permission group
`coreshop_permission_group_coreshop_your_bundle`.

Use the same identifier when you check the permission:

```php
$menuItem->setAttribute('permission', 'coreshop_your_bundle_permission_dashboard');
```

```php
$user->isAllowed('coreshop_your_bundle_permission_dashboard');
```

## Classic admin keys are ignored, not rejected

The `pimcore_admin` section used to carry the classic ExtJS admin's asset registration as well (`js`, `css`,
`editmode_js`, `editmode_css`). Those keys no longer do anything on 2026.x, but `registerPimcoreResources()`
never looks at them rather than rejecting them, so a bundle that still declares them keeps working without
changes. Only the backend-relevant keys are read:

| Key | Purpose |
| --- | --- |
| `permissions` | Pimcore user-permission definitions |
| `install.documents` | Documents installed by `PimcoreDocumentsInstaller` |
| `install.image_thumbnails` | Thumbnail configs installed by `PimcoreImageThumbnailsInstaller` |
| `install.sql` | SQL files run by `SqlInstaller` |

Install types are not validated. A type whose installer no longer exists — `grid_config` and `routes` were
the two the classic admin removal took — is written to a container parameter that nobody reads, so declaring
one is inert rather than an error.

The section name stays `pimcore_admin`, deliberately and permanently. It is a historical name — the section
configures backend resources, not the classic admin UI — but bundles outside the core declare
`pimcore_admin: { permissions: [...] }` and call `registerPimcoreResources()` verbatim, so neither the key
nor the method is renamed, aliased or deprecated. Please do not "clean it up".

## Installing the declarations

Declarations are written to Pimcore by the installers behind:

```bash
$ bin/console coreshop:resources:install
```

Restricting the run to a single application installs only that application's declarations:

```bash
$ bin/console coreshop:resources:install -a coreshop_your_bundle
```

The permission installer is idempotent — permissions that already exist are skipped, so it is safe to run on
every deployment. It never removes permission definitions; if you drop a permission from your bundle, ship a
migration that deletes the row from `users_permission_definitions`.
