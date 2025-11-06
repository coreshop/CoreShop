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
import { ProductBundleIconModule } from './modules/icon-library'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopProductServiceIds } from './modules/product-price-rules/service-ids'
import { NestedCondition, TimespanCondition, WeightCondition } from './modules/product-price-rules/conditions'
import { DiscountAmountAction, DiscountPercentAction, PriceAction, DiscountPriceAction, EmptyAction } from './modules/product-price-rules/actions'
import { ProductPriceRuleManager } from './modules/product-price-rules/ProductPriceRuleManager'

const plugin: IAbstractPlugin = {
    name: 'coreshop-product',

    onInit() {
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
        conditionRegistry.register('timespan', TimespanCondition)
        conditionRegistry.register('weight', WeightCondition)

        // Register Product Price Rule Actions (ProductBundle-specific)
        actionRegistry.register('discountAmount', DiscountAmountAction)
        actionRegistry.register('discountPercent', DiscountPercentAction)
        actionRegistry.register('price', PriceAction)
        actionRegistry.register('discountPrice', DiscountPriceAction)
        actionRegistry.register('notDiscountableCustomAttributes', EmptyAction)
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(ProductBundleIconModule)

        // Register Product Price Rules widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-product-product-price-rules',
            component: ProductPriceRuleManager
        })
    }
}

export default plugin
