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
import { CarrierWidgetsModule } from './modules/carriers/widgets'
import { CarrierManager } from './modules/carriers/CarrierManager'
import { ShippingRuleManager } from './modules/shipping-rules/ShippingRuleManager'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopShippingServiceIds } from './modules/shipping-rules/service-ids'
import { NestedCondition } from '@coreshop/rule/src/rules/conditions'
import type {WidgetRegistry} from "@pimcore/studio-ui-bundle/modules/widget-manager";
import {serviceIds} from "@pimcore/studio-ui-bundle/app";
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { Input } from 'antd'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { CarrierSelect } from './components/CarrierSelect'
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
        container.get<ActionRegistry>(coreshopShippingServiceIds.shippingRuleActionRegistry)

        // Register non-schema custom condition(s).
        // Schema-based conditions/actions are auto-registered at runtime from backend mappings.
        conditionRegistry.register('nested', NestedCondition)
    },

    onStartup({ moduleSystem }) {
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

        // Register CarrierSelect for coreshop_carrier_choice block prefix
        formWidgetRegistry.register('coreshop_carrier_choice', () => ({
            component: CarrierSelect,
        }))

        // Hide ShippingBundle-owned rule collection prefixes from generic schema forms
        const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
        ;[
          'coreshop_shipping_rule_condition_collection',
          'coreshop_product_shipping_action_collection',
        ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

        moduleSystem.registerModule(ShippingBundleIconModule)
        moduleSystem.registerModule(CarrierWidgetsModule)
    }
}

export default plugin
