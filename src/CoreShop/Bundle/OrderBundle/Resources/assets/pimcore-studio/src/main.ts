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
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules'
import { AmountCondition } from './modules/cart-price-rules/conditions'
import { DiscountPercentAction, DiscountAmountAction } from './modules/cart-price-rules/actions'

const plugin: IAbstractPlugin = {
    name: 'coreshop-order',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(OrderBundleIconModule)

        // Register Cart Price Rules widget
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgets.registerWidget({
            name: 'coreshop-order-cart-price-rules',
            component: CartPriceRuleManager
        })

        // Register Cart Price Rule Conditions
        ConditionRegistry.register('amount', AmountCondition)

        // Register Cart Price Rule Actions
        ActionRegistry.register('discountPercent', DiscountPercentAction)
        ActionRegistry.register('discountAmount', DiscountAmountAction)
    }
}

export default plugin
