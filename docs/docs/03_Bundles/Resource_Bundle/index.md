#  Resource Bundle

Resource Bundle is the Heart of CoreShops Model. It handles saving/deleting/updating/creating of CoreShop Models. It handles
Doctrine ORM Mappings and Translations. As well as Routing, Event Dispatching, Serialization and CRUD.

Resource Bundle also takes care about installation of Pimcore Class Definitions, Object Brick Definitions, Field Collection Definitions,
Static Routes and SQL.

You can use Resource Bundle as base for all your Custom Pimcore Entities.

## Installation
```bash
$ composer require coreshop/resource-bundle:^4.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
    ]);
}
```

### Adding custom Doctrine Entities

If you want to create Custom Entites, or if you want to extend existing CoreShop Doctrine Entities, follow this guide:

1. Enable doctrine orm, if not yet enabled and configured properly:
```yaml
# app/config/config.yml
doctrine:
  orm:
    mappings:
      App:
        is_bundle: false
        dir: '%kernel.project_dir%/src/Entity'
        prefix: 'App\Entity'
        alias: App
```

2. Then create your Entity:

```php
# src/Entity/CustomEntity.php
<?php 

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use CoreShop\Component\Resource\Model\SetValuesTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="custom_entity")
 */
class CustomEntity implements \CoreShop\Component\Resource\Model\ResourceInterface
{
    use SetValuesTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;
    
    /**
     * @ORM\Column(name="name", type="string", length=255, nullable="true")
     */
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

This entity gets automatically mapped by Doctrine.

3. Now register a new CoreShop Resource:

```yaml
# app/config/config.yml
core_shop_resource:
  resources:
    app.custom_entity:
      classes:
        model: App\Entity\CustomEntity
        interface: CoreShop\Component\Resource\Model\ResourceInterface
        repository: CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository
```

CoreShop now creates several different Services for you including:
 - Repository: `app.repository.custom_entity`
 - Factory: `app.factory.custom_entity`
 - Controller: `app.admin_controller.custom_entity`

4. You can also let CoreShop create all the routes for you if you add following to your routing config:

```yaml
# config/routes.yaml
coreshop_admin_custom_entity:
    type: coreshop.resources
    resource: |
      alias: app.custom_entity
      # you can also add additional routes here
      additional_routes:
        any_additional_route:
          path: any-additional-route
          action: anyAdditionalAction
          methods:
            - GET
```

This generates following routes for you:
 - `app_custom_entity_get`: `/admin/app/custom_entities/get`
 - `app_custom_entity_list`: `/admin/app/custom_entities/list`
 - `app_custom_entity_add`: `/admin/app/custom_entities/add`
 - `app_custom_entity_save`: `/admin/app/custom_entities/save`
 - `app_custom_entity_delete`: `/admin/app/custom_entities/delete`

5. If you want to use the Controller. You need to configure the serializer path your JMSSerializer and also define a serializer config:

```yaml
# app/config/config.yml
jms_serializer:
    metadata:
        directories:
            app:
                namespace_prefix: "App\\Entity"
                path: "%kernel.project_dir%/config/jms_serializer"
```

```yaml
# config/jms_serializer/CustomEntity.yml
AppBundle\Model\CustomEntity:
  exclusion_policy: ALL
  xml_root_name: custom_entity
  properties:
    id:
      expose: true
      type: integer
      groups: [List, Detailed]
    name:
      expose: true
      type: array
      groups: [List, Detailed]
```

### Adding custom Pimcore Entities

Similar to Doctrine, you can also register Pimcore DataObject Classes as CoreShop Resources and CoreShop creates several Services for you:

1. Your Model should implement `CoreShop\Component\Resource\Model\ResourceInterface`, best way is to extend from `CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel`.
2. Then register your Model as CoreShop Resource:

```yaml
# app/config/config.yml
core_shop_resource:
  pimcore:
    app.my_data_object_class:
      classes:
        model: Pimcore\Model\DataObject\MyDataObjectClass
        interface: CoreShop\Component\Resource\Model\ResourceInterface
```

CoreShop now creates several different Services for you including:
 - Repository: `app.my_data_object_class.repository`
 - Factory: `app.my_data_object_class.factory`

#### Use your Pimcore Entity

You can either use Pimcore Listing Classes like:

```php
$list = new Pimcore\Model\Object\MyDataObjectClass\Listing();
```

Or use CoreShop's Repository Service (this is specially useful if you want to use provide DataObjects within your own Bundles):

```php
$pimcoreEntityObject = $container->get('app.repository.my_data_object_class')->findBy($id);

$list = $container->get('app.repository.my_data_object_class')->getList();
```
