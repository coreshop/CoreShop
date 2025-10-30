/**
 * CoreShop OrderBundle Studio Plugin
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
import { OrderBundleIconModule } from './modules/icon-library'
import { CartPriceRuleManager } from './modules/cart-price-rules/CartPriceRuleManager'
import { coreshopRuleServiceIds } from '@coreshop/rule/src/rules/registry'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry/ConditionRegistry'
import type { ActionRegistry } from '@coreshop/rule/src/rules/registry/ActionRegistry'
import { AmountCondition, VoucherCondition, TimespanCondition, NestedCondition, NotCombinableCondition } from './modules/cart-price-rules/conditions'
import { DiscountPercentAction, DiscountAmountAction, SurchargePercentAction, SurchargeAmountAction, CartItemAction } from './modules/cart-price-rules/actions'
import { CartItemConditionRegistry } from './modules/cart-price-rules/cart-item/CartItemConditionRegistry'
import { CartItemActionRegistry } from './modules/cart-price-rules/cart-item/CartItemActionRegistry'
import { coreshopOrderServiceIds } from './modules/cart-price-rules/cart-item/service-ids'

const plugin: IAbstractPlugin = {
    name: 'coreshop-order',

    onInit() {
        // Register CartItem registries as singleton services in the container
        // This allows other bundles to access them via container.get()
        container.bind(coreshopOrderServiceIds.cartItemActionRegistry).to(CartItemActionRegistry).inSingletonScope()
        container.bind(coreshopOrderServiceIds.cartItemConditionRegistry).to(CartItemConditionRegistry).inSingletonScope()

        // Get the main registries from the container (bound by RuleBundle)
        const conditionRegistry = container.get<ConditionRegistry>(coreshopRuleServiceIds.conditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopRuleServiceIds.actionRegistry)

        // Get the CartItem registries from the container (bound by OrderBundle)
        const cartItemConditionRegistry = container.get<CartItemConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry)
        const cartItemActionRegistry = container.get<CartItemActionRegistry>(coreshopOrderServiceIds.cartItemActionRegistry)

        // Register Cart Price Rule Conditions (OrderBundle-specific)
        conditionRegistry.register('amount', AmountCondition)
        conditionRegistry.register('voucher', VoucherCondition)
        conditionRegistry.register('timespan', TimespanCondition)
        conditionRegistry.register('nested', NestedCondition)
        conditionRegistry.register('not_combinable', NotCombinableCondition)

        // Register Cart Price Rule Actions (OrderBundle-specific)
        actionRegistry.register('discountPercent', DiscountPercentAction)
        actionRegistry.register('discountAmount', DiscountAmountAction)
        actionRegistry.register('surchargePercent', SurchargePercentAction)
        actionRegistry.register('surchargeAmount', SurchargeAmountAction)
        actionRegistry.register('cartItemAction', CartItemAction)

        // Register Cart Item Conditions (reuse existing components)
        cartItemConditionRegistry.register('amount', AmountCondition)

        // Register Cart Item Actions (reuse existing components)
        cartItemActionRegistry.register('discountPercent', DiscountPercentAction)
        cartItemActionRegistry.register('discountAmount', DiscountAmountAction)
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(OrderBundleIconModule)

        // Register Cart Price Rules widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-order-cart-price-rules',
            component: CartPriceRuleManager
        })
    }
}

export default plugin
