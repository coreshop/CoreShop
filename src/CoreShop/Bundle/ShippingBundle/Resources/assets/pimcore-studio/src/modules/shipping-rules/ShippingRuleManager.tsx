/**
 * CoreShop ShippingBundle - Shipping Rule Manager
 */

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { shippingRuleApi } from './api'
import type { ShippingRuleDetail } from './api'
import { SettingsForm } from './components/SettingsForm'
import { coreshopShippingServiceIds } from './service-ids'

export const ShippingRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    shippingRuleApi.getConfig()
      .then(setConfig)
      .catch(err => {
        console.error('Failed to load config:', err)
      })
  }, [])

  return (
    <EntityTabbedManager<ShippingRuleDetail>
      api={shippingRuleApi}
      dragType='coreshop:shipping_rule'
      leftRootTitle={t('coreshop_carriers_shipping_rule', { defaultValue: 'Shipping Rules' })}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => {
        return {
          id: data.id,
          name: data.name,
          active: data.active,
          conditions: data.conditions,
          actions: data.actions
        }
      }}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_carriers_shipping_rule', { defaultValue: 'Add Shipping Rule' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await shippingRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            {t('coreshop_shipping_rule_select', { defaultValue: 'Select a shipping rule to view details.' })}
          </div>
        }

        return (
          <RuleForm
            rule={data}
            config={config}
            conditionRegistryId={coreshopShippingServiceIds.shippingRuleConditionRegistry}
            actionRegistryId={coreshopShippingServiceIds.shippingRuleActionRegistry}
            settingsComponent={
              <SettingsForm
                rule={data}
                onChange={setData}
                currentLocale={ctx?.currentLocale ?? 'en'}
              />
            }
            onChange={setData}
            hideToolbar={true}
          />
        )
      }}
    />
  )
}
