/**
 * CoreShop AddressBundle - State Manager
 *
 * Entity manager for State resources with localization support.
 * States are grouped by country, matching the ExtJS behavior.
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
import { GroupedEntityTabbedManager } from '@coreshop/resource/src/entities/components/GroupedEntityTabbedManager'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { stateApi, type StateDetail } from './api'
import { countryApi } from '../countries/api'
import { StateForm } from './StateForm'

export const StateManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <GroupedEntityTabbedManager<StateDetail>
      api={stateApi}
      dragType="coreshop:state"
      localizable
      loadGroups={async () => await countryApi.list() as any}
      resolveGroupId={(li, groups) => {
        const it: any = li
        if (typeof it.country === 'number') return it.country
        if (typeof it.countryName === 'string') return groups.find(g => g.name === it.countryName)?.id ?? null
        return null
      }}
      applyGroup={(data, groupId) => ({ ...data, country: groupId ?? undefined } as StateDetail)}
      buildSavePayload={(data) => data}
      onAdd={async (groupId?: number) => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_state_add', { defaultValue: 'Add State' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (value: string) => {
            const res = await stateApi.add({ name: value, ...(groupId ? { country: groupId } : {}) })
            resolve(res.data.id)
          }
        })
      })}
      renderDetail={(data, setData, groups, ctx) => {
        if (!data) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              {t('coreshop_state_select', { defaultValue: 'Select a state to view details.' })}
            </div>
          )
        }

        return (
          <StateForm
            data={data}
            currentLocale={ctx?.currentLocale ?? 'en'}
            locales={ctx?.locales}
            onChange={(draft) => setData(draft)}
          />
        )
      }}
    />
  )
}
