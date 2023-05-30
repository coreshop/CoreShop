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

namespace CoreShop\Bundle\PaymentBundle;

use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentCalculatorsPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentRuleActionPass;
use CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler\PaymentRuleConditionPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use CoreShop\Bundle\WorkflowBundle\CoreShopWorkflowBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopPaymentBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopWorkflowBundle(), 1550);
        $collection->addBundle(new CoreShopRuleBundle(), 3500);
        $collection->addBundle(new CoreShopMoneyBundle(), 3600);
        $collection->addBundle(new CoreShopCurrencyBundle(), 2700);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PaymentRuleConditionPass());
        $container->addCompilerPass(new PaymentRuleActionPass());
        $container->addCompilerPass(new PaymentCalculatorsPass());
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Payment\Model';
    }
}
