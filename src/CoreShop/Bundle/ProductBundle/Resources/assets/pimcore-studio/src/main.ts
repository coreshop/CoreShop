/**
 * CoreShop ProductBundle Studio Plugin
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
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { Input } from 'antd'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { ProductBundleIconModule } from './modules/icon-library'
import {
    DynamicTypeObjectDataCoreShopProductUnit,
    DynamicTypeObjectDataCoreShopProductUnitDefinition,
    DynamicTypeObjectDataCoreShopProductUnitDefinitions,
    DynamicTypeObjectDataCoreShopProductSpecificPriceRules
} from './dynamic-types'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from './modules/product-price-rules/service-ids'
import { NestedCondition } from './modules/product-price-rules/conditions'
import { ProductPriceRuleManager } from './modules/product-price-rules/ProductPriceRuleManager'
import { ProductUnitManager } from './modules/product-units/ProductUnitManager'

const plugin: IAbstractPlugin = {
    name: 'coreshop-product',

    onInit() {
        // Register Dynamic Types
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
            serviceIds['DynamicTypes/ObjectDataRegistry']
        )
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopProductUnit())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopProductUnitDefinition())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopProductUnitDefinitions())
        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopProductSpecificPriceRules())

        // Register ProductPriceRule registries as singleton services in the container
        // This allows other bundles to access them via container.get()
        // We use the generic ConditionRegistry and ActionRegistry from RuleBundle
        container.bind(coreshopProductServiceIds.productPriceRuleConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(coreshopProductServiceIds.productPriceRuleActionRegistry).to(ActionRegistry).inSingletonScope()

        // Get the ProductPriceRule registries from the container (bound by ProductBundle)
        const conditionRegistry = container.get<ConditionRegistry>(coreshopProductServiceIds.productPriceRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopProductServiceIds.productPriceRuleActionRegistry)

        // Register Product Price Rule Conditions (ProductBundle-specific)
        conditionRegistry.register('nested', NestedCondition)
        void actionRegistry

        // Register ProductSpecificPriceRule registries as singleton services
        container.bind(coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry).to(ConditionRegistry).inSingletonScope()
        container.bind(coreshopProductServiceIds.productSpecificPriceRuleActionRegistry).to(ActionRegistry).inSingletonScope()

        // Get the ProductSpecificPriceRule registries from the container
        const specificConditionRegistry = container.get<ConditionRegistry>(coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry)
        const specificActionRegistry = container.get<ActionRegistry>(coreshopProductServiceIds.productSpecificPriceRuleActionRegistry)

        // Register Product Specific Price Rule Conditions (same as ProductPriceRules)
        specificConditionRegistry.register('nested', NestedCondition)
        void specificActionRegistry
    },

    onStartup({ moduleSystem }) {
        // Hide ProductBundle-owned rule collection prefixes from generic schema forms
        const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)
        const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
        ;[
          'coreshop_product_price_rule_condition_collection',
          'coreshop_product_price_rule_action_collection',
          'coreshop_product_specific_price_rule_condition_collection',
          'coreshop_product_specific_price_rule_action_collection',
        ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

        moduleSystem.registerModule(ProductBundleIconModule)

        // Register Product Price Rules widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-product-product-price-rules',
            component: ProductPriceRuleManager
        })

        // Register Product Units widget
        widgets.registerWidget({
            name: 'coreshop-product-product-units',
            component: ProductUnitManager
        })
    }
}

export default plugin
