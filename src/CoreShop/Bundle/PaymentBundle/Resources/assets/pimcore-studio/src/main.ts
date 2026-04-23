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
import { Input } from 'antd'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry as StudioFormWidgetRegistry } from '@coreshop/studio-form'
import { EntityChoiceWidget } from '@coreshop/resource/src/components/EntityChoiceWidget'
import { loadPaymentProviders, getPaymentProviderCache } from './components/PaymentProviderSelect'
import { PaymentBundleIconModule } from './modules/icon-library'
import { PaymentProviderWidgetsModule } from './modules/payment-providers/widgets'
import { ConditionRegistry, ActionRegistry } from '@coreshop/rule/src/rules/registry'
import { coreshopPaymentServiceIds } from './modules/payment-provider-rules/service-ids'
import { PaymentProviderManager } from './modules/payment-providers/PaymentProviderManager'
import { PaymentProviderRuleManager } from './modules/payment-provider-rules/PaymentProviderRuleManager'
import { GatewayRegistry } from './modules/payment-providers/gateways'
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

    // Schema-based conditions/actions are auto-registered at runtime from backend mappings.
    // Keep registries instantiated here so other bundles can extend them during startup.
    void conditionRegistry
    void actionRegistry

    // ============================================
    // Gateway Configurator Registry Setup
    // ============================================
    container.bind(coreshopPaymentServiceIds.gatewayConfiguratorRegistry)
      .to(GatewayRegistry)
      .inSingletonScope()

    // GatewayRegistry is available for custom gateway configurators that can't
    // be expressed as Symfony form types. Schema-based gateways are auto-resolved
    // from backend block prefixes in GatewayConfigPanel.

    // ============================================
    // StudioForm Widget Registration
    // ============================================
    const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)

    formWidgetRegistry.register('coreshop_payment_provider_choice', (field) => ({
      component: EntityChoiceWidget,
      props: { loadOptions: loadPaymentProviders, getCachedOptions: getPaymentProviderCache, droppableAccept: 'coreshop:payment_provider', mode: field.multiple ? 'multiple' as const : undefined }
    }))

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
    // Hide PaymentBundle-owned rule collection prefixes from generic schema forms
    const formWidgetRegistry = container.get<StudioFormWidgetRegistry>(widgetRegistryServiceId)
    const hiddenWidget = () => ({ component: Input, extra: { hidden: true } })
    ;[
      'coreshop_payment_provider_rule_condition_collection',
      'coreshop_payment_action_collection',
    ].forEach((prefix) => formWidgetRegistry.register(prefix, hiddenWidget))

    moduleSystem.registerModule(PaymentBundleIconModule)
    moduleSystem.registerModule(PaymentProviderWidgetsModule)
  }
}

export default plugin
