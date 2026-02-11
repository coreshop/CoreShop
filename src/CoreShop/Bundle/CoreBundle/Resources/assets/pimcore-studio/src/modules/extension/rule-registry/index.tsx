/**
 * Rule Registry Extension Module
 *
 * This module extends rule registries from other bundles (OrderBundle, ProductBundle, ShippingBundle)
 * with shared conditions and actions that CoreBundle provides.
 *
 * Because bundles load asynchronously via Module Federation, we use lazy initialization:
 * - Check if registries are bound in the container
 * - If not, defer initialization until they're available
 * - This ensures CoreBundle can extend registries from any bundle regardless of load order
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopOrderServiceIds } from '@coreshop/order/src/modules/cart-price-rules/service-ids'
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
import { coreshopPaymentServiceIds } from '@coreshop/payment/src/modules/payment-provider-rules/service-ids'
import { coreshopQuantityPriceRulesServiceIds } from '@coreshop/productquantitypricerules/src/modules/quantity-price-rules/service-ids'
import { coreshopShippingServiceIds } from '@coreshop/shipping/src/modules/shipping-rules/service-ids'
import {
  CategoriesCondition,
  CarriersCondition,
  CountriesCondition,
  CurrenciesCondition,
  CustomerGroupsCondition,
  CustomersCondition,
  GuestCondition,
  NestedCondition,
  ProductsCondition,
  StoresCondition,
  TimespanCondition,
  ZonesCondition
} from '../../shared/rules/conditions'
import {
  DiscountAmountAction,
  DiscountPercentAction
} from '../../shared/rules/actions'
import { PriceAction } from '@coreshop/product/src/modules/product-price-rules/actions'
import {
  NotCombinableWithCartPriceVoucherRuleCondition,
  QuantityCondition
} from '../../product-price-rules/conditions'

/**
 * Registry extension configuration
 */
interface RegistryExtension {
  serviceId: symbol
  type: 'condition' | 'action'
  registrations: Record<string, any>
}

/**
 * Define all registry extensions
 */
const REGISTRY_EXTENSIONS: RegistryExtension[] = [
  // ============================================
  // Cart Price Rule Extensions (OrderBundle)
  // ============================================
  {
    serviceId: coreshopOrderServiceIds.cartPriceRuleConditionRegistry,
    type: 'condition',
    registrations: {
      carriers: CarriersCondition,
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      nested: NestedCondition,
      products: ProductsCondition,
      stores: StoresCondition,
      timespan: TimespanCondition,
      zones: ZonesCondition
    }
  },
  {
    serviceId: coreshopOrderServiceIds.cartPriceRuleActionRegistry,
    type: 'action',
    registrations: {
      discountAmount: DiscountAmountAction,
      discountPercent: DiscountPercentAction
    }
  },

  // ============================================
  // Product Price Rule Extensions (ProductBundle)
  // ============================================
  {
    serviceId: coreshopProductServiceIds.productPriceRuleConditionRegistry,
    type: 'condition',
    registrations: {
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      nested: NestedCondition,
      not_combinable_with_cart_price_voucher_rule: NotCombinableWithCartPriceVoucherRuleCondition,
      products: ProductsCondition,
      quantity: QuantityCondition,
      stores: StoresCondition,
      timespan: TimespanCondition,
      zones: ZonesCondition
    }
  },
  {
    serviceId: coreshopProductServiceIds.productPriceRuleActionRegistry,
    type: 'action',
    registrations: {
      discountAmount: DiscountAmountAction,
      discountPercent: DiscountPercentAction,
      price: PriceAction
    }
  },

  // ============================================
  // Product Specific Price Rule Extensions (ProductBundle)
  // ============================================
  {
    serviceId: coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry,
    type: 'condition',
    registrations: {
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      nested: NestedCondition,
      not_combinable_with_cart_price_voucher_rule: NotCombinableWithCartPriceVoucherRuleCondition,
      products: ProductsCondition,
      stores: StoresCondition,
      timespan: TimespanCondition,
      zones: ZonesCondition
    }
  },
  {
    serviceId: coreshopProductServiceIds.productSpecificPriceRuleActionRegistry,
    type: 'action',
    registrations: {
      discountAmount: DiscountAmountAction,
      discountPercent: DiscountPercentAction,
      price: PriceAction
    }
  },

  // ============================================
  // Payment Provider Rule Extensions (PaymentBundle)
  // ============================================
  {
    serviceId: coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry,
    type: 'condition',
    registrations: {
      carriers: CarriersCondition,
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      nested: NestedCondition,
      products: ProductsCondition,
      stores: StoresCondition,
      timespan: TimespanCondition,
      zones: ZonesCondition
    }
  },
  {
    serviceId: coreshopPaymentServiceIds.paymentProviderRuleActionRegistry,
    type: 'action',
    registrations: {
      discountAmount: DiscountAmountAction,
      discountPercent: DiscountPercentAction,
      // Note: additionAmount and price are registered in PaymentBundle main.ts
      // They are PaymentBundle-specific but could be moved here as shared actions
    }
  },

  // ============================================
  // Quantity Price Rule Extensions (ProductQuantityPriceRulesBundle)
  // ============================================
  {
    serviceId: coreshopQuantityPriceRulesServiceIds.conditionRegistry,
    type: 'condition',
    registrations: {
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      nested: NestedCondition,
      products: ProductsCondition,
      stores: StoresCondition,
      timespan: TimespanCondition,
      zones: ZonesCondition
    }
  },

  // ============================================
  // Shipping Rule Extensions (ShippingBundle)
  // ============================================
  {
    serviceId: coreshopShippingServiceIds.shippingRuleConditionRegistry,
    type: 'condition',
    registrations: {
      categories: CategoriesCondition,
      countries: CountriesCondition,
      currencies: CurrenciesCondition,
      customerGroups: CustomerGroupsCondition,
      customers: CustomersCondition,
      guest: GuestCondition,
      products: ProductsCondition,
      stores: StoresCondition,
      zones: ZonesCondition
    }
  }
]

// Note: CartItem registries use string IDs instead of Symbols, so we handle them separately
const CART_ITEM_REGISTRIES = {
  conditionRegistryId: coreshopOrderServiceIds.cartItemConditionRegistry,
  actionRegistryId: coreshopOrderServiceIds.cartItemActionRegistry
}

/**
 * Apply registry extensions
 * Registers shared conditions/actions into each registry
 */
function applyRegistryExtensionsInternal(): void {
  // Handle Symbol-based registries
  for (const extension of REGISTRY_EXTENSIONS) {
    // Check if registry is bound in container
    if (!container.isBound(extension.serviceId)) {
      continue
    }

    try {
      // Get the registry (either ConditionRegistry or ActionRegistry)
      const registry = container.get<ConditionRegistry | ActionRegistry>(extension.serviceId)

      // Register all items
      for (const [key, component] of Object.entries(extension.registrations)) {
        registry.register(key, component)
      }
    } catch (error) {
      console.error(`[CoreShop Core] Failed to extend registry ${extension.serviceId.toString()}:`, error)
    }
  }

  // Handle CartItem registries (string-based IDs)
  try {
    if (container.isBound(CART_ITEM_REGISTRIES.conditionRegistryId)) {
      const cartItemConditionRegistry = container.get<ConditionRegistry>(CART_ITEM_REGISTRIES.conditionRegistryId)
      cartItemConditionRegistry.register('categories', CategoriesCondition)
      cartItemConditionRegistry.register('products', ProductsCondition)
    }

    if (container.isBound(CART_ITEM_REGISTRIES.actionRegistryId)) {
      const cartItemActionRegistry = container.get<ActionRegistry>(CART_ITEM_REGISTRIES.actionRegistryId)
      cartItemActionRegistry.register('discountAmount', DiscountAmountAction)
      cartItemActionRegistry.register('discountPercent', DiscountPercentAction)
    }
  } catch (error) {
    console.error('[CoreShop Core] Failed to extend CartItem registries:', error)
  }
}

/**
 * Wait for all registries to be available
 * Retries until all registries are bound or max attempts reached
 */
async function waitForRegistries(maxAttempts: number = 50, interval: number = 100): Promise<void> {
  let attempts = 0

  return new Promise((resolve) => {
    const checkRegistries = () => {
      attempts++

      // Check if all Symbol-based registries are bound
      const allSymbolBound = REGISTRY_EXTENSIONS.every(ext => container.isBound(ext.serviceId))

      // Check if CartItem registries are bound (string-based IDs)
      const cartItemBound =
        container.isBound(CART_ITEM_REGISTRIES.conditionRegistryId) &&
        container.isBound(CART_ITEM_REGISTRIES.actionRegistryId)

      if (allSymbolBound && cartItemBound) {
        resolve()
        return
      }

      // Check which registries are missing
      const missingSymbol = REGISTRY_EXTENSIONS
        .filter(ext => !container.isBound(ext.serviceId))
        .map(ext => ext.serviceId.toString())

      const missingCartItem: string[] = []
      if (!container.isBound(CART_ITEM_REGISTRIES.conditionRegistryId)) {
        missingCartItem.push('CartItem Condition Registry')
      }
      if (!container.isBound(CART_ITEM_REGISTRIES.actionRegistryId)) {
        missingCartItem.push('CartItem Action Registry')
      }

      if (attempts >= maxAttempts) {
        console.warn(
          `[CoreShop Core] Timeout waiting for registries after ${maxAttempts} attempts. Missing:`,
          [...missingSymbol, ...missingCartItem]
        )
        resolve()
        return
      }

      // Retry
      setTimeout(checkRegistries, interval)
    }

    checkRegistries()
  })
}

/**
 * Rule Registry Extension Module
 */
export const RuleRegistryExtensionModule: AbstractModule = {
  async onInit(): Promise<void> {
    // Wait for all registries to be available
    await waitForRegistries()

    // Apply all extensions
    applyRegistryExtensionsInternal()
  }
}
