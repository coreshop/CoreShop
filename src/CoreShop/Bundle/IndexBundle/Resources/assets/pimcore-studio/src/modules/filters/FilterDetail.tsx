/**
 * CoreShop IndexBundle Filter Detail
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
import { Tabs } from 'antd'
import { useTranslation } from 'react-i18next'
import type { Filter, FilterConfig } from './types'
import { SettingsForm } from './components/SettingsForm'
import { ConditionsPanel } from './components/ConditionsPanel'
import { serviceIds } from './service-ids'

interface FilterDetailProps {
  filter: Filter
  config: FilterConfig
  onChange: (filter: Filter) => void
}

export const FilterDetail: React.FC<FilterDetailProps> = ({
  filter,
  config,
  onChange
}) => {
  const { t } = useTranslation()
  const hasIndex = filter.index !== null && filter.index !== undefined

  const tabs = [
    {
      key: 'settings',
      label: t('coreshop_settings', { defaultValue: 'Settings' }),
      children: (
        <SettingsForm
          filter={filter}
          onChange={onChange}
        />
      )
    },
    {
      key: 'pre-conditions',
      label: t('coreshop_filters_pre_conditions', { defaultValue: 'Pre-Conditions' }),
      children: (
        <ConditionsPanel
          conditions={filter.preConditions ?? []}
          availableTypes={config.pre_conditions}
          onChange={(preConditions) => onChange({ ...filter, preConditions })}
          registryId={serviceIds.preConditionRegistry}
          indexId={filter.index ?? undefined}
          disabled={!hasIndex}
        />
      )
    },
    {
      key: 'conditions',
      label: t('coreshop_filters_user_conditions', { defaultValue: 'User Conditions' }),
      children: (
        <ConditionsPanel
          conditions={filter.conditions ?? []}
          availableTypes={config.user_conditions}
          onChange={(conditions) => onChange({ ...filter, conditions })}
          registryId={serviceIds.userConditionRegistry}
          indexId={filter.index ?? undefined}
          disabled={!hasIndex}
        />
      )
    }
  ]

  return (
    <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
      <Tabs
        defaultActiveKey="settings"
        items={tabs}
        style={{ flex: 1, overflow: 'auto' }}
        tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
        destroyInactiveTabPane={false}
      />
    </div>
  )
}
