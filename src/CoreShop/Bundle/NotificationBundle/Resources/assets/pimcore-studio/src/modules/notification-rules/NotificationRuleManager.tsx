/**
 * CoreShop NotificationBundle Studio Plugin
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
import { container } from '@pimcore/studio-ui-bundle'
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { ActionRegistry, ConditionRegistry, registerSchemaComponentsFromMaps } from '@coreshop/rule/src/rules/registry'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { notificationRuleApi } from './api'
import type { NotificationRule, NotificationRuleConfig, NotificationRuleType } from './types'
import { SettingsForm } from './components/SettingsForm'
import { coreshopNotificationServiceIds } from './service-ids'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

export const NotificationRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<NotificationRuleConfig | null>(null)
  const [selectedType, setSelectedType] = React.useState<NotificationRuleType | null>(null)

  // Load config on mount
  React.useEffect(() => {
    notificationRuleApi.getConfig()
      .then((cfg) => {
        const conditionRegistry = container.get<ConditionRegistry>(coreshopNotificationServiceIds.notificationRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopNotificationServiceIds.notificationRuleActionRegistry)

        const conditionMap: Record<string, string> = {}
        const actionMap: Record<string, string> = {}

        for (const type of cfg.types) {
          for (const [conditionName, blockPrefix] of Object.entries(cfg.conditionSchemaByType?.[type] ?? {})) {
            conditionMap[`${type}.${conditionName}`] = blockPrefix
          }

          for (const [actionName, blockPrefix] of Object.entries(cfg.actionSchemaByType?.[type] ?? {})) {
            actionMap[`${type}.${actionName}`] = blockPrefix
          }
        }

        registerSchemaComponentsFromMaps(conditionRegistry, actionRegistry, conditionMap, actionMap, cfg.schemas)
        setConfig(cfg)
      })
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load notification config')))
      })
  }, [])

  // Build a RuleConfig for the selected type
  const getRuleConfigForType = React.useCallback((type: NotificationRuleType | null): RuleConfig => {
    if (!type || !config) {
      return { conditions: [], actions: [] }
    }

    // Get conditions and actions for this type and prefix them with the type
    const typeConditions = config.conditions[type] ?? []
    const typeActions = config.actions[type] ?? []

    return {
      conditions: typeConditions.map(c => `${type}.${c}`),
      actions: typeActions.map(a => `${type}.${a}`)
    }
  }, [config])

  if (!config) {
    return <div style={{ padding: 24, textAlign: 'center' }}>Loading...</div>
  }

  return (
    <EntityTabbedManager<NotificationRule>
      api={notificationRuleApi}
      dragType='coreshop:notification_rule'
      leftRootTitle={t('coreshop_notification_rules', { defaultValue: 'Notification Rules' })}
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_notification_rule_add', { defaultValue: 'Add Notification Rule' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await notificationRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            {t('coreshop_notification_rule_select', { defaultValue: 'Select a notification rule to view details.' })}
          </div>
        }

        // Get current type from data or selected type
        const currentType = data.type ?? selectedType

        // Create rule config based on current type
        const ruleConfig = getRuleConfigForType(currentType)

        return (
          <RuleForm
            rule={data}
            config={ruleConfig}
            conditionRegistryId={coreshopNotificationServiceIds.notificationRuleConditionRegistry}
            actionRegistryId={coreshopNotificationServiceIds.notificationRuleActionRegistry}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
            settingsComponent={
              <SettingsForm
                rule={data}
                onChange={setData}
                types={config.types}
                currentLocale={ctx?.currentLocale ?? 'en'}
                locales={ctx?.locales}
                onTypeChange={(type) => {
                  setSelectedType(type)
                  // Reset conditions and actions when type changes
                  setData({
                    ...data,
                    type,
                    conditions: [],
                    actions: []
                  })
                }}
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
