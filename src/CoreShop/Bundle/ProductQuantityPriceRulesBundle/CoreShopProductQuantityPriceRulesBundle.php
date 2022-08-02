<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesActionPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesCalculatorPass;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler\ProductQuantityPriceRulesConditionPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreShopProductQuantityPriceRulesBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ProductQuantityPriceRulesConditionPass());
        $container->addCompilerPass(new ProductQuantityPriceRulesActionPass());
        $container->addCompilerPass(new ProductQuantityPriceRulesCalculatorPass());
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Product Quantity Price Rules';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Product Quantity Price Rules Bundle';
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\ProductQuantityPriceRules\Model';
    }
}
