<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesActionPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesCalculatorPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesConditionPass;
use CoreShop\Component\ProductQuantityPriceRules\Calculator\CalculatorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Action\ProductQuantityPriceRuleActionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Rule\Condition\QuantityRuleConditionCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;

class CoreShopProductQuantityPriceRulesExtension extends AbstractModelExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', $config['driver'], $config['resources'], $container);
        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);

        $container->setParameter('coreshop.product_quantity_price_rules.ranges.action_constraints', $config['action_constraints']);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(ProductQuantityPriceRuleActionInterface::class)
            ->addTag(ProductQuantityPriceRulesActionPass::PRODUCT_QUANTITY_PRICE_RULE_ACTION_TAG);

        $container
            ->registerForAutoconfiguration(CalculatorInterface::class)
            ->addTag(ProductQuantityPriceRulesCalculatorPass::PRODUCT_QUANTITY_PRICE_RULE_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(QuantityRuleConditionCheckerInterface::class)
            ->addTag(ProductQuantityPriceRulesConditionPass::PRODUCT_QUANTITY_PRICE_RULE_CONDITION_TAG);
    }
}
