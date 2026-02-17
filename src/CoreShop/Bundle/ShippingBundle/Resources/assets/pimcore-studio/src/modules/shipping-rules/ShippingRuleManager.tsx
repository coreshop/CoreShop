/**
 * CoreShop ShippingBundle - Shipping Rule Manager
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { EntityTabbedManager, getErrorMessage, renderApiError } from '@coreshop/resource'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { ActionRegistry, ConditionRegistry, registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules/registry'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { shippingRuleApi } from './api'
import type { ShippingRuleDetail } from './api'
import { SettingsForm } from './components/SettingsForm'
import { coreshopShippingServiceIds } from './service-ids'

export const ShippingRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const modal = useFormModal()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    shippingRuleApi.getConfig()
      .then((cfg) => {
        const conditionRegistry = container.get<ConditionRegistry>(coreshopShippingServiceIds.shippingRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopShippingServiceIds.shippingRuleActionRegistry)
        registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, cfg)
        setConfig(cfg)
      })
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load config')))
      })
  }, [])

  return (
    <EntityTabbedManager<ShippingRuleDetail>
      api={shippingRuleApi}
      dragType='coreshop:shipping_rule'
      leftRootTitle={t('coreshop_carriers_shipping_rule', { defaultValue: 'Shipping Rules' })}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
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
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
            settingsComponent={
              <SettingsForm
                rule={data}
                onChange={setData}
                currentLocale={ctx?.currentLocale ?? 'en'}
                locales={ctx?.locales}
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
