# Adding a new Pimcore Entity with automated installation

1. Add a new Pimcore Class in Pimcore.
2. Add a Parent Class to your Pimcore Entity
3. Export Class Definition to ```AppBundle/Resources/install/pimcore/classes/PimcoreEntity.json```

## Create Parent Class

```php
<?php
//AppBundle/Model/PimcoreEntityInterface.php

interface PimcoreEntityInterface extends ResourceInterface
    public function getName($language = null);

    public function setName($name, $language = null);
}
```

```php
<?php
//AppBundle/Model/PimcoreEntity.php

class PimcoreEntity extends AbstractPimcoreModel implements PimcoreEntityInterface, PimcoreModelInterface {
    public function getName($language = null) {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setName($name, $language = null) {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
```

## Create Dependency Injection Configuration

```php
<?php
//AppBundle/DependencyInjection/Configuration.php

namespace AppBundle\DependencyInjection;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app_custom');

        $this->addModelsSection($rootNode);

        return $treeBuilder;
    }

    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('pimcore_entity')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('PimcoreEntity')->end()
                                ->arrayNode('options')
                                    ->scalarNode('path')->defaultValue('path/within/pimcore')->end()
                                    ->scalarNode('permission')->defaultValue('pimcore_entity')->cannotBeOverwritten()->end()
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\Object\PimcoreEntity')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PimcoreEntity::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@AppBundle/Resources/install/pimcore/classes/PimcoreEntity.json')->end()
                                        ->scalarNode('type')->defaultValue(CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT)->cannotBeOverwritten(true)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
```

```php
<?php
//AppBundle/DependencyInjection/AppBundleExtension.php

namespace AppBundle\DependencyInjection;

final class AppBundleExtension extends AbstractModelExtension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $this->registerPimcoreModels('app', $config['pimcore'], $container);
    }
}

```


## Use your Pimcore Entity

You can either use Pimcore Listing Classes like:

```php
$list = new Pimcore\Model\Object\PimcoreEntity\Listing();
```

or use automated generated Factory/Repository Classes

```php
$pimcoreEntityObject = $container->get('app.repository.pimcore_entity')->findBy($id);

$list = $container->get('app.repository.pimcore_entity')->getList();
```
