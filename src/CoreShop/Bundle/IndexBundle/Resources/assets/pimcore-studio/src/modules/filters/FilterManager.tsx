/**
 * CoreShop IndexBundle Filter Manager
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
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { filterApi } from './api'
import type { Filter, FilterConfig } from './types'
import { FilterDetail } from './FilterDetail'
import type { ConditionRegistry } from './conditions/ConditionRegistry'
import { serviceIds } from './service-ids'
import { registerFilterSchemaConditionsFromMap } from './registerFilterSchemaConditions'

export const FilterManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const [config, setConfig] = React.useState<FilterConfig | null>(null)

  // Load config on mount and register schema-based conditions
  React.useEffect(() => {
    filterApi.getConfig()
      .then((cfg) => {
        const preCondReg = container.get<ConditionRegistry>(serviceIds.preConditionRegistry)
        const userCondReg = container.get<ConditionRegistry>(serviceIds.userConditionRegistry)

        registerFilterSchemaConditionsFromMap(preCondReg, cfg.preConditionSchemaByType, cfg.schemas)
        registerFilterSchemaConditionsFromMap(userCondReg, cfg.userConditionSchemaByType, cfg.schemas)

        setConfig(cfg)
      })
      .catch(err => {
        console.error('Failed to load filter config:', err)
      })
  }, [])

  return (
    <EntityTabbedManager<Filter>
      api={filterApi}
      dragType='coreshop:filter'
      leftRootTitle={t('coreshop_filters', { defaultValue: 'Filters' })}
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => ({
        id: data.id,
        name: data.name,
        resultsPerPage: data.resultsPerPage,
        orderDirection: data.orderDirection,
        orderKey: data.orderKey,
        index: data.index,
        preConditions: data.preConditions,
        conditions: data.conditions,
      })}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_filters_add', { defaultValue: 'Add Filter' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await filterApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData) => {
        if (!data) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              {t('coreshop_filters_select', { defaultValue: 'Select a filter to view details.' })}
            </div>
          )
        }

        if (!config) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              {t('coreshop_loading_configuration', { defaultValue: 'Loading configuration...' })}
            </div>
          )
        }

        return (
          <FilterDetail
            filter={data}
            config={config}
            onChange={setData}
          />
        )
      }}
    />
  )
}
