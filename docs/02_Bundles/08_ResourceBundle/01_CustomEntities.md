# Adding a new custom entity

## Create Translatable Entity

First of all, we need to create our Entity Class. In this case, we create a Translatable Entity.

```php
<?php

//AppBundle/Model/CustomEntityInterface.php

interface CustomEntityInterface extends ResourceInterface, TranslatableInterface {
    public function getName($language = null);

    public function setName($name, $language = null);
}
```

```php
<?php

//AppBundle/Model/CustomEntity.php

interface CustomEntity implements CustomEntityInterface {
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    protected $id;
    protected $name;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName($language = null)
    {
        return $this->getTranslation($language)->getName();
    }

    public function setName($name, $language = null)
    {
        $this->getTranslation($language, false)->setName($name);

        return $this;
    }

    protected function createTranslation()
    {
        return new CustomEntityTranslation();
    }
}
```

Since our Entity is Translatable, we need to add our Translation Entity as well.

```php
<?php

//AppBundle/Model/CustomEntityTranslationInterface.php

interface CustomEntityTranslationInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);
}
```

```php
<?php

//AppBundle/Model/CustomEntityTranslation.php

class CustomEntityTranslation extends AbstractTranslation implements CountryTranslationInterface
{
    protected $id;
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
```

## Create Doctrine Configuration

```yaml
# AppBundle/Resources/config/doctrine/model/CustomEntity.orm.yml

AppBundle\Model\CustomEntity:
  type: mappedSuperclass
  table: app_custom_entity
  fields:
    id:
      type: integer
      column: id
      id: true
      generator:
        strategy: AUTO
```

Our Translation Doctrine definition:

```yaml
# AppBundle/Resources/config/doctrine/model/CustomEntityTranslation.orm.yml

AppBundle\Model\CustomEntityTranslation:
  type: mappedSuperclass
  table: app_custom_entity_translation
  fields:
    id:
      type: integer
      column: id
      id: true
      generator:
        strategy: AUTO
    name:
      type: string
      column: name
```

## Create DI Configuration

```php
<?php

//AppBundle/DependencyInjection/Configuration.php

namespace CoreShop\Bundle\AddressBundle\DependencyInjection;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(CoreShopResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;
        $this->addModelsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('country')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('permission')->defaultValue('country')->cannotBeOverwritten()->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CustomEntity::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomEntityInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('admin_controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(CustomEntityRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(CustomEntityType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CustomEntityTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CustomEntityTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(CustomEntityTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
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

//AppBundle/DependencyInjection/AppExtension.php

final class AppExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        //'app' is the application name
        $this->registerResources('app', $config['driver'], $config['resources'], $container);
    }
}

```

```php
<?php

//AppBundle/DependencyInjection/AppExtension.php

final class AppBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    protected function getModelNamespace()
    {
        return 'AppBundle\Model';
    }
}

```


## Create Serialization Definition if you want to serialize your Entity

```yaml
# AppBundle/Resources/config/serializer/Model.CustomEntity.yml

AppBundle\Model\CustomEntity:
  exclusion_policy: ALL
  xml_root_name: custom_entity
  properties:
    id:
      expose: true
      type: integer
      groups: [List, Detailed]
    translations:
      expose: true
      type: array
      groups: [Detailed]
  virtual_properties:
    getName:
      serialized_name: name
      groups: [List, Detailed]
```

```yaml
# AppBundle/Resources/config/serializer/Model.CustomEntityTranslation.yml

AppBundle\Model\CustomEntityTranslation:
  exclusion_policy: ALL
  xml_root_name: custom_entity_translation
  properties:
    name:
      expose: true
      type: string
      groups: [Detailed]
```

## Create Routes to ResourceController
```yaml
# AppBundle/Resources/config/pimcore/routing.yml

app_custom_entity:
  type: coreshop.resources
  resource: |
    alias: app.custom_entity

```

This will create all CRUD routes: (app is the application name specified in AppExtension.php)

GET: /admin/app/custom_entity/list
GET: /admin/app/custom_entity/get
POST: /admin/app/custom_entity/add
POST: /admin/app/custom_entity/save
DELETE: /admin/app/custom_entity/delete