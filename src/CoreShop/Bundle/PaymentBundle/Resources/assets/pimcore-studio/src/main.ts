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

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { PaymentBundleIconModule } from './modules/icon-library'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopPaymentServiceIds } from './modules/payment-provider-rules/service-ids'
import { AmountCondition, PaymentProviderRuleCondition } from './modules/payment-provider-rules/conditions'
import { AdditionPercentAction, AdditionAmountAction, DiscountPercentAction, PaymentProviderRuleAction, PriceAction } from './modules/payment-provider-rules/actions'
import { PaymentProviderManager } from './modules/payment-providers/PaymentProviderManager'
import { PaymentProviderRuleManager } from './modules/payment-provider-rules/PaymentProviderRuleManager'
import { PaymentProviderRuleFormBuilderModule } from './modules/payment-provider-rules/form-builder-module'
import { GatewayRegistry, PayPalExpressCheckoutConfigurator, SofortConfigurator } from './modules/payment-providers/gateways'

const plugin: IAbstractPlugin = {
  name: 'coreshop-payment',

  onInit() {
    // ============================================
    // Payment Provider Rules Registry Setup
    // ============================================
    // Create and bind registries for Payment Provider Rules (SYNCHRONOUS!)
    container.bind(coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry)
      .to(ConditionRegistry)
      .inSingletonScope()

    container.bind(coreshopPaymentServiceIds.paymentProviderRuleActionRegistry)
      .to(ActionRegistry)
      .inSingletonScope()

    // Get registries
    const conditionRegistry = container.get<ConditionRegistry>(
      coreshopPaymentServiceIds.paymentProviderRuleConditionRegistry
    )
    const actionRegistry = container.get<ActionRegistry>(
      coreshopPaymentServiceIds.paymentProviderRuleActionRegistry
    )

    // Register PaymentBundle-specific conditions
    conditionRegistry.register('amount', AmountCondition)
    conditionRegistry.register('paymentProviderRule', PaymentProviderRuleCondition)

    // Register PaymentBundle-specific actions
    actionRegistry.register('additionPercent', AdditionPercentAction)
    actionRegistry.register('additionAmount', AdditionAmountAction)
    actionRegistry.register('discountPercent', DiscountPercentAction)
    actionRegistry.register('paymentProviderRule', PaymentProviderRuleAction)
    actionRegistry.register('price', PriceAction)

    // ============================================
    // Gateway Configurator Registry Setup
    // ============================================
    container.bind(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)
      .to(GatewayRegistry)
      .inSingletonScope()

    const gatewayRegistry = container.get<GatewayRegistry>(
      coreshopPaymentServiceIds.gatewayConfiguratorRegistry
    )

    // Register PaymentBundle-specific gateway configurators
    gatewayRegistry.register('paypal_express_checkout', PayPalExpressCheckoutConfigurator)
    gatewayRegistry.register('sofort', SofortConfigurator)

    // ============================================
    // Widget Registration
    // ============================================
    const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgets.registerWidget({
      name: 'coreshop_payment_providers',
      component: PaymentProviderManager
    })

    widgets.registerWidget({
      name: 'coreshop_payment_provider_rules',
      component: PaymentProviderRuleManager
    })
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(PaymentBundleIconModule)
    moduleSystem.registerModule(PaymentProviderRuleFormBuilderModule)
  }
}

export default plugin
