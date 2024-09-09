# Adding a New Pimcore Entity with Class Installation

## Step 1: Add a New Pimcore Class in Pimcore

1. Create a new class in Pimcore.
2. Add a Parent Class to your Pimcore Entity.
3. Export Class Definition to `AppBundle/Resources/install/pimcore/classes/PimcoreEntity.json`.

## Step 2: Create Parent Class

### PimcoreEntityInterface

Create `PimcoreEntityInterface.php` in the `AppBundle/Model` directory.

```php
<?php
// AppBundle/Model/PimcoreEntityInterface.php

interface PimcoreEntityInterface extends ResourceInterface {
    public function getName($language = null);
    public function setName($name, $language = null);
}
```

### PimcoreEntity

Create `PimcoreEntity.php` in the `AppBundle/Model` directory.

```php
<?php
// AppBundle/Model/PimcoreEntity.php

class PimcoreEntity extends AbstractPimcoreModel implements PimcoreEntityInterface, PimcoreModelInterface {
    public function getName($language = null) {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setName($name, $language = null) {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
```

## Step 3: Create Dependency Injection Configuration

### Configuration.php

Create `Configuration.php` in `AppBundle/DependencyInjection`.

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

### AppBundleExtension.php

Create `AppBundleExtension.php` in the same directory.

```php
<?php
//AppBundle/DependencyInjection/AppBundleExtension.php

namespace AppBundle\DependencyInjection;

final class AppBundleExtension extends AbstractModelExtension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $this->registerPimcoreModels('app', $config['pimcore'], $container);
    }
}
```

## Step 4: Use Your Pimcore Entity

You can either use Pimcore Listing Classes or the automatically generated Factory/Repository Classes.

### Using Pimcore Listing Classes

```php
$list = new Pimcore\Model\Object\PimcoreEntity\Listing();
```

### Using Factory/Repository Classes

```php
$pimcoreEntityObject = $container->get('app.repository.pimcore_entity')->findBy($id);

$list = $container->get('app.repository.pimcore_entity')->getList();
```