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
import { coreshopShippingServiceIds } from './modules/shipping-rules/service-ids'
import {
    WeightCondition,
    AmountCondition,
    PostcodesCondition,
    DimensionCondition,
    ShippingRuleCondition
} from './modules/shipping-rules/conditions'
import {
    AdditionPercentAction,
    AdditionAmountAction,
    DiscountPercentAction,
    DiscountAmountAction,
    PriceAction,
    ShippingRuleAction
} from './modules/shipping-rules/actions'
import { NestedCondition, TimespanCondition } from '@coreshop/core/src/modules/shared/rules/conditions'
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
        conditionRegistry.register('weight', WeightCondition)
        conditionRegistry.register('amount', AmountCondition)
        conditionRegistry.register('postcodes', PostcodesCondition)
        conditionRegistry.register('dimension', DimensionCondition)
        conditionRegistry.register('shippingRule', ShippingRuleCondition)
        conditionRegistry.register('nested', NestedCondition)
        conditionRegistry.register('timespan', TimespanCondition)

        // Register ShippingBundle-specific actions
        actionRegistry.register('additionPercent', AdditionPercentAction)
        actionRegistry.register('additionAmount', AdditionAmountAction)
        actionRegistry.register('discountPercent', DiscountPercentAction)
        actionRegistry.register('discountAmount', DiscountAmountAction)
        actionRegistry.register('price', PriceAction)
        actionRegistry.register('shippingRule', ShippingRuleAction)
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(ShippingBundleIconModule)
    }
}

export default plugin
