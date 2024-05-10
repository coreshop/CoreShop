# Frontend Bundle

The CoreShop Store Front, also known as the FrontendBundle, provides a default implementation that serves as a practical
guide for working with the CoreShop Framework. It is designed to showcase how various functionalities can be implemented
in a store environment.

## Components of the Frontend Bundle

The FrontendBundle includes various components, each focusing on a specific aspect of the store front:

- **Controllers**: The bundle includes a set of controllers that manage the interactions between the store front's user
  interface and the underlying CoreShop functionalities. These controllers can be customized to fit the specific needs
  of your store.
    - [Learn more about Controllers](./01_Controllers.md)

## DEMO

The CoreShop Frontend is a fully functional store front that can be used as a reference for building your own store. It
is meant as a Demo and not as a production-ready store.

## Best Practice

Best practice is not to use any Templates from the Demo Frontend, but to create your own Templates and use the
Controllers from the Demo Frontend.

## Copy Templates

To copy the Templates from the Demo Frontend, you can use the following command:

```bash
cp -R vendor/coreshop/core-shop/src/CoreShop/Bundle/FrontendBundle/Resources/views templates/coreshop
```

Overwrite the `TemplateConfiguratorInterface` by creating a new Service and decorate the original one:

```php
<?php
// src/CoreShop/TemplateConfigurator/TemplateConfigurator.php
declare(strict_types=1);

namespace App\CoreShop\TemplateConfigurator;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;

class TemplateConfigurator implements TemplateConfiguratorInterface
{
    public function findTemplate(string $templateName): string
    {
        return sprintf('coreshop/%s.twig', $templateName);
    }
}
```

And configure the new Service:

```yaml
# config/services.yaml
services:
  App\CoreShop\TemplateConfigurator\TemplateConfigurator:
    decorates: 'CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfigurator'
```
