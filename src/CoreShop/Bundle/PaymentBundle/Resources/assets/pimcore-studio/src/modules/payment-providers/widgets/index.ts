/**
 * CoreShop PaymentBundle - Payment Provider Widgets Module
 *
 * Registers schema form widgets for payment provider compound types.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { AbstractModule } from '@pimcore/studio-ui-bundle'
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry } from '@coreshop/studio-form'
import { GatewayConfigWidget } from './GatewayConfigWidget'
import { PaymentProviderRuleGroupWidget } from './PaymentProviderRuleGroupWidget'

export const PaymentProviderWidgetsModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

    // GatewayConfigType compound field → GatewayConfigPanel
    widgetRegistry.register('gateway_config', () => ({
      component: GatewayConfigWidget,
    }))

    // PaymentProviderRuleGroupCollectionType → PaymentProviderRuleGroupPanel
    widgetRegistry.register('payment_provider_rule_group_collection', () => ({
      component: PaymentProviderRuleGroupWidget,
    }))
  }
}
