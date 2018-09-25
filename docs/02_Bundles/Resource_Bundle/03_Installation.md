# Adding new files that needs to be installed

Resource Bundles takes care about installing your resources. It can handle following types:
    - Object Classes
    - Field Collection Classes
    - Objectbrick Classes
    - Routes
    - Permissions
    - SQL Files

## Object Classes, Field Collections and Objectbrick Classes
To install object classes, you need to configure your classes inside your Bundle and register them to Resource Bundle. (as described [here](02_PimcoreEntities.md))

## Routes, SQL and Permissions
To install routes, permissions or execute sql files, configure them in your Bundle likes this:

```php
<?php

namespace AppBundle\DependencyInjection;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('AppBundle');

        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('routes')->defaultValue(['@AppBundle/Resources/install/pimcore/routes.yml'])->end()
                            ->scalarNode('sql')->defaultValue(['@AppBundle/Resources/install/pimcore/data.sql'])->end()
                        ->end()
                    ->end()
                    ->scalarNode('permissions')
                        ->cannotBeOverwritten()
                        ->defaultValue(['name_of_permission'])
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}

```

## Routes Exmaple File
```yaml
yeah_route:
  pattern: "/(\\w+)\\/yeah-route/"
  reverse: "/%_locale/yeah\-route"
  module: AppBundle
  controller: "@app.frontend.controller.controller"
  action: doSomething
  variables: _locale
  priority: 2
```