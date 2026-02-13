/**
 * CoreShop ShippingBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { ShippingBundleIconModule } from './modules/icon-library'
import { CarrierManager } from './modules/carriers/CarrierManager'
import { ShippingRuleManager } from './modules/shipping-rules/ShippingRuleManager'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { createSchemaCondition, createSchemaAction } from '@coreshop/rule/src/rules/components'
import { coreshopShippingServiceIds } from './modules/shipping-rules/service-ids'
import { NestedCondition } from '@coreshop/core/src/modules/shared/rules/conditions'
import type {WidgetRegistry} from "@pimcore/studio-ui-bundle/modules/widget-manager";
import {serviceIds} from "@pimcore/studio-ui-bundle/app";
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import {
    DynamicTypeObjectDataCoreShopCarrier,
    DynamicTypeObjectDataCoreShopCarrierMultiselect
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-shipping',

    onInit() {
        // Register Dynamic Types
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCarrier())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopCarrierMultiselect())

        // Register Carrier widget
        const widgetManager = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgetManager.registerWidget({
            name: 'coreshop-shipping-carriers',
            component: CarrierManager
        })

        // Register ShippingRule widget
        widgetManager.registerWidget({
            name: 'coreshop-shipping-shipping-rules',
            component: ShippingRuleManager
        })

        // Create and bind ShippingRule registries
        container.bind(coreshopShippingServiceIds.shippingRuleConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(coreshopShippingServiceIds.shippingRuleActionRegistry).to(ActionRegistry).inSingletonScope()

        // Get registries
        const conditionRegistry = container.get<ConditionRegistry>(coreshopShippingServiceIds.shippingRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopShippingServiceIds.shippingRuleActionRegistry)

        // Register ShippingBundle-specific conditions
        conditionRegistry.register('weight', createSchemaCondition('coreshop_shipping_rule_condition_weight'))
        conditionRegistry.register('amount', createSchemaCondition('coreshop_shipping_rule_condition_amount'))
        conditionRegistry.register('postcodes', createSchemaCondition('coreshop_shipping_rule_condition_postcode'))
        conditionRegistry.register('dimension', createSchemaCondition('coreshop_shipping_rule_condition_dimension'))
        conditionRegistry.register('shippingRule', createSchemaCondition('coreshop_shipping_rule_condition_shipping_rule'))
        conditionRegistry.register('nested', NestedCondition)

        // Register ShippingBundle-specific actions
        actionRegistry.register('additionPercent', createSchemaAction('coreshop_shipping_rule_action_addition_percent'))
        actionRegistry.register('additionAmount', createSchemaAction('coreshop_shipping_rule_action_addition_amount'))
        actionRegistry.register('discountPercent', createSchemaAction('coreshop_shipping_rule_action_discount_percent'))
        actionRegistry.register('discountAmount', createSchemaAction('coreshop_shipping_rule_action_discount_amount'))
        actionRegistry.register('price', createSchemaAction('coreshop_shipping_rule_action_price'))
        actionRegistry.register('shippingRule', createSchemaAction('coreshop_shipping_rule_condition_shipping_rule'))
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(ShippingBundleIconModule)
    }
}

export default plugin
