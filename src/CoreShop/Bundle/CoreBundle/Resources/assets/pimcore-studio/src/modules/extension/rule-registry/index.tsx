/**
 * Rule Registry Extension Module
 *
 * Keeps custom non-schema rule components (like nested conditions) registered
 * across registries. Schema-based components are auto-registered from backend
 * type->blockPrefix mappings at runtime.
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopOrderServiceIds } from '@coreshop/order/src/modules/cart-price-rules/service-ids'
import { coreshopProductServiceIds } from '@coreshop/product/src/modules/product-price-rules/service-ids'
import { coreshopPaymentServiceIds } from '@coreshop/payment/src/modules/payment-provider-rules/service-ids'
import { coreshopQuantityPriceRulesServiceIds } from '@coreshop/productquantitypricerules/src/modules/quantity-price-rules/service-ids'
import { NestedCondition } from '@coreshop/rule/src/rules/conditions'

const NESTED_CONDITION_REGISTRIES: Array<symbol | string> = [
  coreshopOrderServiceIds.cartPriceRuleConditionRegistry,
  coreshopProductServiceIds.productPriceRuleConditionRegistry,
  coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry,
  coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry,
  coreshopQuantityPriceRulesServiceIds.conditionRegistry,
  coreshopOrderServiceIds.cartItemConditionRegistry,
]

function registerNestedCondition(registryId: symbol | string): void {
  if (!container.isBound(registryId)) {
    return
  }

  try {
    const registry = container.get<ConditionRegistry>(registryId)
    if (!registry.has('nested')) {
      registry.register('nested', NestedCondition)
    }
  } catch (error) {
    console.error(`[CoreShop Core] Failed to register nested condition for ${registryId.toString()}:`, error)
  }
}

async function waitForRegistries(maxAttempts: number = 50, interval: number = 100): Promise<void> {
  let attempts = 0

  return new Promise((resolve) => {
    const checkRegistries = () => {
      attempts++

      const allBound = NESTED_CONDITION_REGISTRIES.every(registryId => container.isBound(registryId))
      if (allBound || attempts >= maxAttempts) {
        resolve()
        return
      }

      setTimeout(checkRegistries, interval)
    }

    checkRegistries()
  })
}

export const RuleRegistryExtensionModule: AbstractModule = {
  async onInit(): Promise<void> {
    await waitForRegistries()

    for (const registryId of NESTED_CONDITION_REGISTRIES) {
      registerNestedCondition(registryId)
    }
  }
}
