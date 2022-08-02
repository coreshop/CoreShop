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

namespace CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\Collector\RuleCollector;
use CoreShop\Component\Rule\Condition\RuleConditionsValidationProcessorInterface;
use CoreShop\Component\Rule\Condition\TraceableRuleConditionsValidationProcessor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TraceableValidationProcessorPass implements CompilerPassInterface
{
    public const RULE_CONDITIONS_VALIDATIONS_PROCESSOR = 'coreshop.rule.conditions.validation_processor';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $validationProcessors = [];

        foreach ($container->findTaggedServiceIds(self::RULE_CONDITIONS_VALIDATIONS_PROCESSOR) as $serviceId => $tags) {
            $newServiceId = sprintf('%s.decorated', $serviceId);
            $serviceIdInner = sprintf('%s.inner', $newServiceId);

            $decorator = new Definition(TraceableRuleConditionsValidationProcessor::class);
            $decorator->setDecoratedService($serviceId);
            $decorator->setArguments([new Reference($serviceIdInner)]);
            $decorator->setPublic(true);

            $container->setDefinition($newServiceId, $decorator);

            $validationProcessors[] = new Reference($newServiceId);
        }

        if (count($validationProcessors) > 0) {
            $collector = new Definition(RuleCollector::class);
            $collector->setArguments([
                $validationProcessors,
            ]);
            $collector->addTag('data_collector', [
                'template' => '@CoreShopRule/Collector/rule.html.twig',
                'id' => 'coreshop.rule_collector',
            ]);

            $container->setDefinition('coreshop.rule_collector', $collector);
        }
    }
}
