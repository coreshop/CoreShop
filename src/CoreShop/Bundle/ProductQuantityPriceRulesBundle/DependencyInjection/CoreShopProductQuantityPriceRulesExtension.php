<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesActionPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesCalculatorPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesConditionPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Condition\QuantityRuleConditionCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

class CoreShopProductQuantityPriceRulesExtension extends AbstractModelExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $config['resources'], $container);
        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);

        $container->setParameter('coreshop.product_quantity_price_rules.ranges.action_constraints', $config['action_constraints']);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(ProductQuantityPriceRuleActionInterface::class)
            ->addTag(ProductQuantityPriceRulesActionPass::PRODUCT_QUANTITY_PRICE_RULE_ACTION_TAG)
        ;

        $container
            ->registerForAutoconfiguration(CalculatorInterface::class)
            ->addTag(ProductQuantityPriceRulesCalculatorPass::PRODUCT_QUANTITY_PRICE_RULE_CALCULATOR_TAG)
        ;

        $container
            ->registerForAutoconfiguration(QuantityRuleConditionCheckerInterface::class)
            ->addTag(ProductQuantityPriceRulesConditionPass::PRODUCT_QUANTITY_PRICE_RULE_CONDITION_TAG)
        ;
    }
}
