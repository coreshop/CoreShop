# Adding a New Custom Doctrine Entity

## Step 1: Create Translatable Entity

First, create your Entity Class. In this case, we create a Translatable Entity.

### CustomEntityInterface

Create `CustomEntityInterface.php` in the `AppBundle/Model` directory.

```php
<?php

interface CustomEntityInterface extends ResourceInterface, TranslatableInterface {
    public function getName($language = null);
    public function setName($name, $language = null);
}
```

### CustomEntity

Create `CustomEntity.php` in the `AppBundle/Model` directory.

```php
<?php

class CustomEntity implements CustomEntityInterface {
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    protected $id;
    protected $name;

    public function __construct() {
        $this->initializeTranslationsCollection();
    }

    // Getters and Setters
}
```

### CustomEntityTranslationInterface

Create `CustomEntityTranslationInterface.php` in the `AppBundle/Model` directory.

```php
<?php

interface CustomEntityTranslationInterface extends ResourceInterface, TimestampableInterface {
    public function getName();
    public function setName($name);
}
```

### CustomEntityTranslation

Create `CustomEntityTranslation.php` in the `AppBundle/Model` directory.

```php
<?php

class CustomEntityTranslation extends AbstractTranslation implements CustomEntityTranslationInterface {
    protected $id;
    protected $name;

    // Getters and Setters
}
```

## Step 2: Create Doctrine Configuration

### CustomEntity.orm.yml

Create `CustomEntity.orm.yml` in `AppBundle/Resources/config/doctrine/model`.

```yaml
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

### CustomEntityTranslation.orm.yml

Create `CustomEntityTranslation.orm.yml` in the same directory.

```yaml
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

## Step 3: Create Dependency Injection Configuration

### Configuration.php

Create `Configuration.php` in `AppBundle/DependencyInjection`.

```php
<?php

namespace CoreShop\Bundle\AddressBundle\DependencyInjection;

final class Configuration implements ConfigurationInterface {
    // Configuration Implementation
}
```

### AppExtension.php

Create `AppExtension.php` in the same directory.

```php
<?php

final class AppExtension extends AbstractModelExtension {
    // Extension Implementation
}
```

### AppBundle.php

Create `AppBundle.php` in `AppBundle`.

```php
<?php

final class AppBundle extends AbstractResourceBundle {
    // Bundle Implementation
}
```

## Step 4: Create Serialization Definition

### Model.CustomEntity.yml

Create `Model.CustomEntity.yml` in `AppBundle/Resources/config/serializer`.

```yaml
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

### Model.CustomEntityTranslation.yml

Create `Model.CustomEntityTranslation.yml` in the same directory.

```yaml
AppBundle\Model\CustomEntityTranslation:
  exclusion_policy: ALL
  xml_root_name: custom_entity_translation
  properties:
    name:
      expose: true
      type: string
      groups: [Detailed]
```

## Step 5: Create Routes

### routing.yml

Create `routing.yml` in `AppBundle/Resources/config/pimcore`.

```yaml
app_custom_entity:
  type: coreshop.resources
  resource: |
    alias: app.custom_entity
```

It will define the following routes:
- `GET`: `/admin/app/custom_entity/list`
- `GET`: `/admin/app/custom_entity/get`
- `POST`: `/admin/app/custom_entity/add`
- `POST`: `/admin/app/custom_entity/save`
- `DELETE`: `/admin/app/custom_entity/delete`
