# CoreShop Fixture Bundle

Fixture Bundle allows us to Install certain data needed for runtime of CoreShop or for the Demo.

## Installation
```bash
$ composer require coreshop/fixture-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\FixtureBundle\CoreShopFixtureBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

#### Creating a new Fixture

Create a new File in your Bundle within the Namespace `Fixtures\Data\Application` for app fixtures and `Fixtures\Data\Demo` for Demo fixtures. The FixtureBundle
will automatically recognize your fixtures.

```php
<?php

namespace CoreShop\Bundle\CoreBundle\Fixtures\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigurationFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $configurations = [
            'system.guest.checkout' => true,
            'system.category.list.mode' => 'list',
            'system.category.list.per_page' => [12, 24, 36],
            'system.category.list.per_page.default' => 12,
            'system.category.grid.per_page' => [5, 10, 15, 20, 25],
            'system.category.grid.per_page.default' => 10,
            'system.category.variant_mode' => 'hide',
            'system.order.prefix' => 'O',
            'system.order.suffix' => '',
            'system.quote.prefix' => 'Q',
            'system.quote.suffix' => '',
            'system.invoice.prefix' => 'IN',
            'system.invoice.suffix' => '',
            'system.invoice.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
            'system.shipment.prefix' => 'SH',
            'system.shipment.suffix' => '',
            'system.shipment.wkhtml' => '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5',
        ];

        foreach ($configurations as $key => $value) {
            $this->container->get('coreshop.configuration.service')->set($key, $value);
        }
    }
}

```
