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
import { CarrierExtensionModule } from './modules/extension/carrier'
import { ComprehensiveExtensionExample } from './modules/extension/comprehensive-example'
import { CategoriesCondition, ProductsCondition, CustomersCondition, CustomerGroupsCondition, GuestCondition, CountriesCondition, ZonesCondition, StoresCondition, CurrenciesCondition, CarriersCondition } from './modules/shared/rules/conditions'
import { FreeShippingAction, GiftProductAction, VoucherCreditAction } from './modules/cart-price-rules/actions'
import { CartItemDiscountAmountAction, CartItemDiscountPercentAction, CartItemProductsCondition, CartItemCategoriesCondition } from './modules/cart-price-rules/cart-item'
import { DiscountAmountAction, DiscountPercentAction } from './modules/shared/rules/actions'
import { NotCombinableWithCartPriceVoucherRuleCondition, QuantityCondition } from './modules/product-price-rules/conditions'

// Import registry types from RuleBundle (generic registries)
import type { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'

// Import service IDs from OrderBundle, ProductBundle, and ShippingBundle
// @ts-ignore
import { coreshopOrderServiceIds } from '@coreshop/order/src/modules/cart-price-rules/service-ids'
// @ts-ignore
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
// @ts-ignore
import { coreshopShippingServiceIds } from '@coreshop/shipping/src/modules/shipping-rules/service-ids'

const plugin: IAbstractPlugin = {
    name: 'coreshop-core',

    onInit() {
        // Get the CartPriceRule registries from the container (bound by OrderBundle)
        const cartPriceRuleConditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartPriceRuleConditionRegistry)
        const cartPriceRuleActionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartPriceRuleActionRegistry)

        // Get the ProductPriceRule registries from the container (bound by ProductBundle)
        const productPriceRuleConditionRegistry = container.get<ConditionRegistry>(coreshopProductServiceIds.productPriceRuleConditionRegistry)
        const productPriceRuleActionRegistry = container.get<ActionRegistry>(coreshopProductServiceIds.productPriceRuleActionRegistry)

        // Get the ShippingRule registries from the container (bound by ShippingBundle)
        const shippingRuleConditionRegistry = container.get<ConditionRegistry>(coreshopShippingServiceIds.shippingRuleConditionRegistry)
        const shippingRuleActionRegistry = container.get<ActionRegistry>(coreshopShippingServiceIds.shippingRuleActionRegistry)

        // Get the CartItem registries from the container (bound by OrderBundle)
        const cartItemConditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry)
        const cartItemActionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartItemActionRegistry)

        // Register shared Conditions into CartPriceRule, ProductPriceRule, and ShippingRule registries
        // These conditions are available for all rule types
        const sharedConditions = [
            { type: 'categories', component: CategoriesCondition },
            { type: 'products', component: ProductsCondition },
            { type: 'customers', component: CustomersCondition },
            { type: 'customerGroups', component: CustomerGroupsCondition },
            { type: 'guest', component: GuestCondition },
            { type: 'countries', component: CountriesCondition },
            { type: 'zones', component: ZonesCondition },
            { type: 'stores', component: StoresCondition },
            { type: 'currencies', component: CurrenciesCondition }
        ]

        sharedConditions.forEach(({ type, component }) => {
            cartPriceRuleConditionRegistry.register(type, component)
            productPriceRuleConditionRegistry.register(type, component)
            shippingRuleConditionRegistry.register(type, component)
        })

        // Register Carriers condition only for CartPriceRule (not for ShippingRule - carriers select shipping rules, not vice versa)
        cartPriceRuleConditionRegistry.register('carriers', CarriersCondition)

        // Register ProductPriceRule-specific Conditions (CoreBundle, needs OrderBundle for CartPriceRule API)
        productPriceRuleConditionRegistry.register('not_combinable_with_cart_price_voucher_rule', NotCombinableWithCartPriceVoucherRuleCondition)
        productPriceRuleConditionRegistry.register('quantity', QuantityCondition)

        // Register shared Actions into BOTH CartPriceRule AND ProductPriceRule registries
        // Note: ProductBundle has its own currency-aware versions, so these are the non-currency versions
        const sharedActions = [
            { type: 'discountAmount', component: DiscountAmountAction },
            { type: 'discountPercent', component: DiscountPercentAction }
        ]

        sharedActions.forEach(({ type, component }) => {
            cartPriceRuleActionRegistry.register(type, component)
        })

        // Register Cart-specific Actions (only in CartPriceRule registry)
        cartPriceRuleActionRegistry.register('freeShipping', FreeShippingAction)
        cartPriceRuleActionRegistry.register('giftProduct', GiftProductAction)
        cartPriceRuleActionRegistry.register('voucherCredit', VoucherCreditAction)

        // Register Cart Item Actions (CoreBundle-specific, only in CartItem registry)
        cartItemActionRegistry.register('discountAmount', CartItemDiscountAmountAction)
        cartItemActionRegistry.register('discountPercent', CartItemDiscountPercentAction)

        // Register Cart Item Conditions (CoreBundle-specific, only in CartItem registry)
        cartItemConditionRegistry.register('products', CartItemProductsCondition)
        cartItemConditionRegistry.register('categories', CartItemCategoriesCondition)
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CoreBundleIconModule)
        moduleSystem.registerModule(CoreBundleMenuModule)
        moduleSystem.registerModule(CountryExtensionModule)
        moduleSystem.registerModule(TaxRuleGroupExtensionModule)
        moduleSystem.registerModule(CarrierExtensionModule)
        moduleSystem.registerModule(ComprehensiveExtensionExample)
    }
}

export default plugin
