/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {IAbstractPlugin, container} from '@pimcore/studio-ui-bundle'
import { RuleBundleIconModule } from './modules/icon-library'
import { ActionRegistry, ConditionRegistry, coreshopRuleServiceIds } from './rules/registry'
import { Input } from 'antd'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { TimestampDatePicker } from './widgets/TimestampDatePicker'

const plugin: IAbstractPlugin = {
    name: 'coreshop-rule',

    onInit() {
        // Register ActionRegistry and ConditionRegistry as singleton services in the container
        // This allows other bundles to access them via container.get()
        container.bind(coreshopRuleServiceIds.actionRegistry).to(ActionRegistry).inSingletonScope()
        container.bind(coreshopRuleServiceIds.conditionRegistry).to(ConditionRegistry).inSingletonScope()
    },

    onStartup({ moduleSystem }) {
        // Symfony/Twig-like block-prefix override:
        // Rule collections are rendered in dedicated RuleForm tabs, not inside generic settings schema.
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)
        const hiddenRuleCollectionWidget = () => ({
            component: Input,
            extra: {
                hidden: true,
            },
        })
        // Only hide generic rule collection prefixes owned by RuleBundle.
        // Bundle-specific prefixes are hidden by their respective bundles.
        const collectionPrefixesToHide = [
            'coreshop_rule_condition_collection',
            'coreshop_rule_action_collection',
        ]

        collectionPrefixesToHide.forEach((prefix) => {
            formWidgetRegistry.register(prefix, hiddenRuleCollectionWidget)
        })

        formWidgetRegistry.register('coreshop_timestamp_date', () => ({
            component: TimestampDatePicker,
        }))

        moduleSystem.registerModule(RuleBundleIconModule)
    }
}

export default plugin
