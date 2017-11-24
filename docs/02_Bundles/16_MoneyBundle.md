# CoreShop Money Bundle

## Installation
```bash
$ composer require coreshop/money-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

If you're not using CoreShop bundles, you will also need to add CoreShopResourceBundle and its dependencies
to kernel. Don’t worry, everything was automatically installed via Composer.

```php
<?php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new \JMS\SerializerBundle\JMSSerializerBundle(),

        new \CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),

        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    );
}
```

### Loading JS/CSS Resources into Pimcore

Since the MoneyBundle is a regular Symfony Bundle and not a Pimcore Bundle, you need to take care of loading JS Resources yourself. CoreShop already comes with a helper of doing that:

1. Extend your AppBundle from ```Pimcore\Extension\Bundle\AbstractPimcoreBundle```
2. Implement ```getJsPaths``` and ```getCssPaths``` methods:

```php
<?php

namespace AppBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class AppBundle extends AbstractPimcoreBundle
{
    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        $jsFiles = [];

        if ($this->container->hasParameter('coreshop.application.pimcore.admin.js')) {
             $jsFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.application.pimcore.admin.js'));
        }

        return $jsFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        $cssFiles = [];

        if ($this->container->hasParameter('coreshop.application.pimcore.admin.css')) {
             $cssFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.application.pimcore.admin.css'));
        }

        return $cssFiles;
    }
}
```

## Usage

Money Bundle adds a new core-extension to pimcore which allows you to store currency values as integer.

You also get a Twig Extension to format money values.

```twig
{{ value|coreshop_format_money('€', 'de'); }}
```

