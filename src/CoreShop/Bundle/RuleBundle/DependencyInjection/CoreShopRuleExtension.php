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

namespace CoreShop\Bundle\RuleBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Bundle\RuleBundle\Attribute\AsRuleAvailabilityAssessor;
use CoreShop\Bundle\RuleBundle\Attribute\AsRuleConditionsValidationProcessor;
use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RuleAvailabilityAssessorPass;
use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\TraceableValidationProcessorPass;
use CoreShop\Component\Registry\Autoconfiguration;
use CoreShop\Component\Rule\Condition\Assessor\RuleAvailabilityAssessorInterface;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopRuleExtension extends AbstractModelExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);

        if (array_key_exists('pimcore_admin', $configs)) {
            $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreStudioUiBundle', $bundles)) {
            $loader->load('services/studio.yml');
        }

        $loader->load('services.yml');

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            RuleAvailabilityAssessorInterface::class,
            RuleAvailabilityAssessorPass::RULE_AVAILABILITY_ASSESSOR_TAG,
            AsRuleAvailabilityAssessor::class,
            $configs['autoconfigure_with_attributes'],
        );

        Autoconfiguration::registerForAutoConfiguration(
            $container,
            RuleConditionsValidationProcessorInterface::class,
            TraceableValidationProcessorPass::RULE_CONDITIONS_VALIDATIONS_PROCESSOR,
            AsRuleConditionsValidationProcessor::class,
            $configs['autoconfigure_with_attributes'],
        );
    }
}
