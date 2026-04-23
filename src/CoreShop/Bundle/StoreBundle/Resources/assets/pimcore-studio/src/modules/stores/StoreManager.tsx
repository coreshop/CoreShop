/**
 * CoreShop StoreBundle Studio Plugin
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
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import { storeApi, type StoreDetail } from './api'
import { StoreForm } from './StoreForm'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'

export const StoreManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <EntityTabbedManager<StoreDetail>
      api={storeApi}
      dragType="coreshop:store"
      leftRootTitle={t('coreshop_stores', { defaultValue: 'Stores' })}
      getTitle={(li, data) => data?.name ?? li?.name ?? `Store #${data?.id ?? li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_store_add', { defaultValue: 'Add Store' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await storeApi.add({ name: nameValue })
            if (res.data.id !== undefined) {
              resolve(res.data.id)
            }
          }
        })
      })}
      renderDetail={(data, setData) => (
        <StoreForm data={data} onChange={setData} />
      )}
    />
  )
}
