# CoreShop Pimcore Bundle

## Installation
```bash
$ composer require coreshop/pimcore-bundle:^3.0
```

### Activating Bundle
You need to enable the bundle inside the kernel or with the Pimcore Extension Manager.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\PimcoreBundle\CoreShopPimcoreBundle()
    ]);
}
```

## Usage

The CoreShopPimcoreBundle integrates the CoreShop Pimcore Component into Symfony automatically registers a lot of services for you.

### JS/CSS Resource Loading
With Pimcore, every bundle needs to take care about loading static assets themselve. PimcoreBundle helps you out here, follow these steps to use it:

- Create a DependencyInjection Extension class like:

```php
<?php

namespace AppBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AppExtension extends AbstractModelExtension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
    }
}
```

- Create a DependencyInjection Configuration class like:

```php
<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $this->addPimcoreResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addPimcoreResourcesSection(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('pimcore_admin')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('js')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('test')->defaultValue('/bundles/app/pimcore/js/test.js')->end()
                        ->end()
                    ->end()
                    ->arrayNode('css')
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('test')->defaultValue('/bundles/app/pimcore/css/pimcore.css')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}

```

- That's it, PimcoreBundle now takes care about loading your resources and also bundles them in non DEV-Mode.

### DataObject Extensions

#### Serialized Data
This extension allows you to store SerializedData inside a Pimcore DataObject.

### Slug

Pimcore comes with quite useful objects slugs. But it doesn't come with a Slug Generator. CoreShop for the rescue. In Order to use it,
your class needs to implement `CoreShop\Component\Pimcore\Slug\SluggableInterface` and CoreShop automatically generates slugs for you.

#### Extensions / Influence the slug generation

If you want to change the generated slug or prefix it, you can use the `CoreShop\Component\Pimcore\Event\SlugGenerationEvent` Event.

```
<?php

declare(strict_types=1);

namespace App\EventListener;

use CoreShop\Component\Pimcore\Event\SlugGenerationEvent;
use Pimcore\Model\DataObject\PressRelease;
use Pimcore\Model\Document;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SlugEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            SlugGenerationEvent::class => 'onGenerate',
        ];
    }

    public function onGenerate(SlugGenerationEvent $event): void
    {
        $event->setSlug($event->getSlug() . '-bar');
    }
}
```


### Data Object Features

#### Class Converter and Data Migrate
Class converter is a small utility, which lets you migrate all Data from one class to another. Usage:

```php
<?php

use CoreShop\Component\Pimcore\Migrate;

$currentClassName = 'Product';
$newClassName = 'NewProduct';
$options = [
    'delete_existing_class' => true,
    'parentClass' => 'AppBundle\Model\MyProduct'
];

//Copies $currentClassName Definition to $newClassName
//$options can overwrite some properties like parentClass
Migrate::migrateClass($currentClassName, $newClassName, $options);

//This function migrates all data from $currentClassName to $newClassName
//It uses SQL Commands to increase performance of migration
Migrate::migrateData($currentClassName, $newClassName);
```

#### Class Installer
Class Installer helps you importing Classes/FieldCollections/ObjectBricks into Pimcore based of a JSON Definition:

```php

use CoreShop\Component\Pimcore\ClassInstaller;

$installer = new ClassInstaller();

// For Bricks use
$installer->createBrick($pathToJson, $brickName);

// For Classes use
$installer->createClass($pathToJson, $className, $updateExistingClass);

// For FieldCollections use
$installer->createFieldCollection($pathToJson, $fcName);

```

#### Class/Brick/Field Collection Updater
Definition Updaters help you in migrating your Pimcore Class/Bricks or Field Collection Definitions to be properly
migrated from Release to Release.

To update a Pimcore class use it like this:

```php
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;

$classUpdater = new ClassUpdate('Product');

//Your JSON Definition from Pimcore
$payment = [
    'fieldtype' => 'coreShopSerializedData',
    'phpdocType' => 'array',
    'allowedTypes' =>
        [
        ],
    'maxItems' => 1,
    'name' => 'paymentSettings',
    'title' => 'Payment Settings',
    'tooltip' => '',
    'mandatory' => false,
    'noteditable' => true,
    'index' => false,
    'locked' => null,
    'style' => '',
    'permissions' => null,
    'datatype' => 'data',
    'columnType' => null,
    'queryColumnType' => null,
    'relationType' => false,
    'invisible' => false,
    'visibleGridView' => false,
    'visibleSearch' => false,
];

//Check if field exists
if (!$classUpdater->hasField('paymentSettings')) {
    //If not insert field after a specific field and save the definition
    $classUpdater->insertFieldAfter('paymentProvider', $payment);
    $classUpdater->save();
}

```

Thats it, the same works for FieldCollections with the class `CoreShop\Component\Pimcore\DataObject\FieldCollectionDefinitionUpdate`
and for Bricks with the class `CoreShop\Component\Pimcore\DataObject\BrickDefinitionUpdate`

#### Inheritance Helper
Inhertiance Helper is a small little but very useful helper class to enable Pimcore inheritance only with a closure function like this:

```php

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;

$inheritedValue = InheritanceHelper::useInheritedValues(function() use($object) {
    return $object->getValueInherited();
}, true);

```

#### Version Helper
Version Helper is a small little but very useful helper class to disabling or enablind Pimcore Versioning.

```php

use CoreShop\Component\Pimcore\DataObject\VersionHelper;

VersionHelper::useVersioning(function() use($object) {
    //Object will be saved without creating a new Version
    $object->save();
}, false);

```

#### Unpublished Helper
Unpublsihed Helper is a small little but very useful helper class to get unpublished objects in Pimcore Frontend.

```php

use CoreShop\Component\Pimcore\DataObject\UnpublishedHelper;

$allProducts = UnpublishedHelper::hideUnpublished(function() use($object) {
    //Will return all products, even the unpbulished ones
    return $object->getProducts();
}, false);

```

### Expression Language Features
CoreShop adds some features to the Symfony Expression language like:

- PimcoreLanguageProvider: to get Pimcore Objects, Assets or Documents inside a Expression Language Query

### Migration Features

#### Pimcore Shared Translations
Helps you to install new Shared Translations during Migration:

```php
use CoreShop\Component\Pimcore\Migration\SharedTranslation;

SharedTranslation::add('key', 'en', 'value');
```
