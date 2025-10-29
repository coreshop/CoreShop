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
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm, type RuleFormTab } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { cartPriceRuleApi } from './api'
import type { CartPriceRule } from './types'
import { SettingsForm } from './components/SettingsForm'
import { VoucherCodesPanel } from './components/VoucherCodesPanel'

export const CartPriceRuleManager: React.FC = () => {
  const modal = useFormModal()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    cartPriceRuleApi.getConfig()
      .then(setConfig)
      .catch(err => {
        console.error('Failed to load config:', err)
      })
  }, [])

  return (
    <EntityTabbedManager<CartPriceRule>
      api={cartPriceRuleApi}
      dragType='coreshop:cart_price_rule'
      leftRootTitle='Cart Price Rules'
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => ({
        id: data.id,
        name: data.name,
        description: data.description,
        active: data.active,
        priority: data.priority,
        isVoucherRule: data.isVoucherRule,
        conditions: data.conditions,
        actions: data.actions,
        translations: data.translations
      })}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Cart Price Rule',
          label: 'Name',
          rule: { required: true, message: 'Name is required' },
          onOk: async (nameValue: string) => {
            const res = await cartPriceRuleApi.add({ name: nameValue })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            Select a cart price rule to view details.
          </div>
        }

        const additionalTabs: RuleFormTab[] = [
          {
            key: 'voucher-codes',
            label: 'Voucher Codes',
            disabled: !data.isVoucherRule,
            component: (
              <VoucherCodesPanel
                rule={data}
                disabled={!data.isVoucherRule}
              />
            )
          }
        ]

        return (
          <RuleForm
            rule={data}
            config={config}
            settingsComponent={
              <SettingsForm
                rule={data}
                onChange={setData}
              />
            }
            additionalTabs={additionalTabs}
            onChange={setData}
            hideToolbar={true}
          />
        )
      }}
    />
  )
}
