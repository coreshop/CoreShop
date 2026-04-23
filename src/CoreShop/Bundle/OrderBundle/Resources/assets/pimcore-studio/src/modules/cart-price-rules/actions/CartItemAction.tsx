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

import React from 'react'
import { Tabs, Spin } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import type { ActionComponentProps } from '@coreshop/rule/src/rules'
import type { RuleCondition, RuleAction } from '@coreshop/rule/src/rules/types'
import { CartItemConditionsPanel } from '../cart-item/CartItemConditionsPanel'
import { CartItemActionsPanel } from '../cart-item/CartItemActionsPanel'
import { cartPriceRuleApi } from '../api'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface CartItemConfig {
  conditions: string[]
  actions: string[]
}

export const CartItemAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<CartItemConfig | null>(null)
  const [loading, setLoading] = React.useState(true)

  const conditions = (data.conditions as RuleCondition[]) || []
  const actions = (data.actions as RuleAction[]) || []

  // Load cart item config
  React.useEffect(() => {
    cartPriceRuleApi.getCartItemConfig()
      .then(cfg => {
        setConfig(cfg)
        setLoading(false)
      })
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load cart item config')))
        setLoading(false)
      })
  }, [])

  const handleConditionsChange = (newConditions: RuleCondition[]) => {
    onChange({ ...data, conditions: newConditions })
  }

  const handleActionsChange = (newActions: RuleAction[]) => {
    onChange({ ...data, actions: newActions })
  }

  if (loading || !config) {
    return (
      <div style={{ padding: 24, textAlign: 'center' }}>
        <Spin tip="Loading cart item configuration..." />
      </div>
    )
  }

  const tabItems = [
    {
      key: 'conditions',
      label: 'Conditions',
      children: (
        <CartItemConditionsPanel
          conditions={conditions}
          availableTypes={config.conditions}
          onChange={handleConditionsChange}
        />
      )
    },
    {
      key: 'actions',
      label: 'Actions',
      children: (
        <CartItemActionsPanel
          actions={actions}
          availableTypes={config.actions}
          onChange={handleActionsChange}
        />
      )
    }
  ]

  return (
    <div>
      <Tabs items={tabItems} defaultActiveKey="conditions" />
    </div>
  )
}
