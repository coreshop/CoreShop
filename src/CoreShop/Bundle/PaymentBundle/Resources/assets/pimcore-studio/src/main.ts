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
import { DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { PaymentBundleIconModule } from './modules/icon-library'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { createSchemaCondition, createSchemaAction } from '@coreshop/rule/src/rules/components'
import { coreshopPaymentServiceIds } from './modules/payment-provider-rules/service-ids'
import { PaymentProviderManager } from './modules/payment-providers/PaymentProviderManager'
import { PaymentProviderRuleManager } from './modules/payment-provider-rules/PaymentProviderRuleManager'
import { GatewayRegistry, PayPalExpressCheckoutConfigurator, SofortConfigurator } from './modules/payment-providers/gateways'
import {
    DynamicTypeObjectDataCoreShopPaymentProvider,
    DynamicTypeObjectDataCoreShopPaymentProviderMultiselect
} from './dynamic-types'

const plugin: IAbstractPlugin = {
  name: 'coreshop-payment',

  onInit() {
    // ============================================
    // Dynamic Types Registration
    // ============================================
    const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
      serviceIds['DynamicTypes/ObjectDataRegistry']
    )
    objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopPaymentProvider())
    objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopPaymentProviderMultiselect())

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
    conditionRegistry.register('amount', createSchemaCondition('coreshop_payment_provider_rule_condition_amount'))
    conditionRegistry.register('paymentProviderRule', createSchemaCondition('coreshop_payment_provider_rule_condition_payment_provider_rule'))

    // Register PaymentBundle-specific actions
    actionRegistry.register('additionPercent', createSchemaAction('coreshop_payment_provider_rule_action_addition_percent'))
    actionRegistry.register('additionAmount', createSchemaAction('coreshop_shipping_rule_action_addition_amount'))
    actionRegistry.register('discountPercent', createSchemaAction('coreshop_payment_provider_rule_action_discount_percent'))
    actionRegistry.register('paymentProviderRule', createSchemaAction('coreshop_payment_provider_rule_condition_payment_provider_rule'))
    actionRegistry.register('price', createSchemaAction('coreshop_payment_provider_rule_action_price'))

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
  }
}

export default plugin
