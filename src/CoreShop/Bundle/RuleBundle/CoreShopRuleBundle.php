<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\RuleBundle;

use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RuleAvailabilityAssessorPass;
use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\TraceableValidationProcessorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopRuleBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new TraceableValidationProcessorPass());
        $container->addCompilerPass(new RuleAvailabilityAssessorPass());
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Rule\Model';
    }
}
