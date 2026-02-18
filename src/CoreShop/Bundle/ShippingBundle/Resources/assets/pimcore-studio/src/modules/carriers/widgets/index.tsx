/**
 * CoreShop ShippingBundle - Carrier Widgets Module
 *
 * Registers schema form widget for shipping rule group collection.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import type { AbstractModule } from '@pimcore/studio-ui-bundle'
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry } from '@coreshop/studio-form'
import { ShippingRuleGroupPanel } from '../ShippingRuleGroupPanel'
import type { ShippingRuleAssignment } from '../api'

const ShippingRuleGroupWidget: React.FC<{
  value?: ShippingRuleAssignment[]
  onChange?: (value: ShippingRuleAssignment[]) => void
}> = ({ value, onChange }) => {
  return (
    <ShippingRuleGroupPanel
      ruleGroups={value ?? []}
      onChange={(ruleGroups) => onChange?.(ruleGroups)}
    />
  )
}

export const CarrierWidgetsModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

    // ShippingRuleGroupCollectionType → ShippingRuleGroupPanel
    widgetRegistry.register('shipping_rule_group_collection', () => ({
      component: ShippingRuleGroupWidget,
    }))
  }
}
