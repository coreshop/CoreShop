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

// @ts-ignore
import {IAbstractPlugin, container} from '@pimcore/studio-ui-bundle'
import { CoreBundleIconModule } from './modules/icon-library'
import { CoreBundleMenuModule } from './modules/menu'
import { CountryExtensionModule } from './modules/extension/country'
import { TaxRuleGroupExtensionModule } from './modules/extension/tax-rule-group'
import { coreshopRuleServiceIds } from '@coreshop/rule/src/rules/registry'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry/ConditionRegistry'
import type { ActionRegistry } from '@coreshop/rule/src/rules/registry/ActionRegistry'
import { CategoriesCondition, ProductsCondition, CustomersCondition, CustomerGroupsCondition, GuestCondition, CountriesCondition, ZonesCondition, StoresCondition, CurrenciesCondition, CarriersCondition } from './modules/cart-price-rules/conditions'
import { FreeShippingAction, GiftProductAction, VoucherCreditAction } from './modules/cart-price-rules/actions'
import { CartItemDiscountAmountAction, CartItemDiscountPercentAction, CartItemProductsCondition, CartItemCategoriesCondition } from './modules/cart-price-rules/cart-item'

// Import CartItem registry types and service IDs from OrderBundle
// @ts-ignore
import { coreshopOrderServiceIds } from '@coreshop/order/src/modules/cart-price-rules/cart-item/service-ids'
// @ts-ignore
import type { CartItemConditionRegistry } from '@coreshop/order/src/modules/cart-price-rules/cart-item/CartItemConditionRegistry'
// @ts-ignore
import type { CartItemActionRegistry } from '@coreshop/order/src/modules/cart-price-rules/cart-item/CartItemActionRegistry'

const plugin: IAbstractPlugin = {
    name: 'coreshop-core',

    onInit() {
        // Get the main registries from the container (bound by RuleBundle)
        const conditionRegistry = container.get<ConditionRegistry>(coreshopRuleServiceIds.conditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopRuleServiceIds.actionRegistry)

        // Get the CartItem registries from the container (bound by OrderBundle)
        const cartItemConditionRegistry = container.get<CartItemConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry)
        const cartItemActionRegistry = container.get<CartItemActionRegistry>(coreshopOrderServiceIds.cartItemActionRegistry)

        // Register Cart Price Rule Conditions (CoreBundle-specific)
        // These conditions depend on Core component features
        // Registered in onInit to ensure they're available before any UI renders
        conditionRegistry.register('categories', CategoriesCondition)
        conditionRegistry.register('products', ProductsCondition)
        conditionRegistry.register('customers', CustomersCondition)
        conditionRegistry.register('customerGroups', CustomerGroupsCondition)
        conditionRegistry.register('guest', GuestCondition)
        conditionRegistry.register('countries', CountriesCondition)
        conditionRegistry.register('zones', ZonesCondition)
        conditionRegistry.register('stores', StoresCondition)
        conditionRegistry.register('currencies', CurrenciesCondition)
        conditionRegistry.register('carriers', CarriersCondition)

        // Register Cart Price Rule Actions (CoreBundle-specific)
        // These actions depend on Core component features
        actionRegistry.register('freeShipping', FreeShippingAction)
        actionRegistry.register('giftProduct', GiftProductAction)
        actionRegistry.register('voucherCredit', VoucherCreditAction)

        // Register Cart Item Actions (CoreBundle-specific)
        cartItemActionRegistry.register('discountAmount', CartItemDiscountAmountAction)
        cartItemActionRegistry.register('discountPercent', CartItemDiscountPercentAction)

        // Register Cart Item Conditions (CoreBundle-specific)
        cartItemConditionRegistry.register('products', CartItemProductsCondition)
        cartItemConditionRegistry.register('categories', CartItemCategoriesCondition)
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(CountryExtensionModule)
        moduleSystem.registerModule(TaxRuleGroupExtensionModule)
    }
}

export default plugin
