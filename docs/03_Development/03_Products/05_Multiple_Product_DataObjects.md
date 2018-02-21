# CoreShop Multiple Product DataObjects

CoreShop comes with one pre-installed Product Class (CoreShopProduct), which in most cases is enough. In some cases, you might want to use separated classes with different purposes. For example a ProductSet, which consists of multiple Products but also needs to be available for complex price calculations like Price Rules.

First of all, we need to create a new DataObject Class in Pimcore. A basic Purchasable Product only needs to implement [```\CoreShop\Component\Order\Model\PurchasableInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Model/PurchasableInterface.php) but since we want to allow complex price calculation, we need to implement [```CoreShop\Component\Core\Model\ProductInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Model/ProductInterface.php).

 > **Note**
 > If your Product is very simple and you do not need complex price calculations
 > you then only need to implement [```\CoreShop\Component\Order\Model\PurchasableInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Order/Model/PurchasableInterface.php)

Easiest way to create the new class is:

 - **1**: Open Pimcore DataObject Editor
 - **2**: Add a new Class called ProductSet
 - **3**: Import CoreShop Default Product Class ([```CoreShopProduct.json```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/CoreBundle/Resources/install/pimcore/classes/CoreShopProductBundle/CoreShopProduct.json))
 - **4**: Adapt to your needs
 - **5**: Register your ProductSet Class to CoreShop:

```php
<?php

//src/AppBundle/DependencyInjection/Configuration.php

namespace AppBundle\DependencyInjection;

use CoreShop\Bundle\ProductBundle\Pimcore\Repository\ProductRepository;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
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

    //Add your configuration here
    private function addModelsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pimcore')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product_set')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->scalarNode('path')->defaultValue('products')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Pimcore\Model\DataObject\ProductSet')->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(PimcoreFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ProductRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('install_file')->defaultValue('@CoreShopProductBundle/Resources/install/pimcore/classes/CoreShopProduct.json')->end()
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

//src/AppBundle/DependencyInjection/AppExtension.php

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

        //Register the model to the container
        $this->registerPimcoreModels('app', $config['pimcore'], $container);

        $loader->load('services.yml');
    }
}
```
 - **6**: You can now use the new ObjectClass as you wish, CoreShop automatically recognises it as ProductClass and your are allowed to use it in PriceRules.

 > **Note:**
 > For the Class to be allowed as href/multihref in Pimcore, you need to adapt following classes as well:
 >  - CoreShopCartItem
 >  - CoreShopOrderItem
 >  - CoreShopQuoteItem
 >
 > Check for the product field and add your new ProductSet there as allowed references.
