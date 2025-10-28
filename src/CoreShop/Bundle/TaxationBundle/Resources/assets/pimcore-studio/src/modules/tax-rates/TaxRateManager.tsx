/**
 * CoreShop TaxationBundle Studio Plugin
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
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { taxRateApi, type TaxRateDetail } from './api'
import { TaxRateForm } from './TaxRateForm'

export const TaxRateManager: React.FC = () => {
  const modal = useFormModal()

  return (
    <EntityTabbedManager<TaxRateDetail>
      api={ taxRateApi }
      dragType='coreshop:tax_rate'
      leftRootTitle='Tax Rates'
      localizable
      getTitle={ (li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}` }
      buildSavePayload={ (data) => ({
        id: data.id,
        name: data.name,
        rate: data.rate,
        active: data.active,
        translations: data.translations
      }) }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Tax Rate',
          label: 'Name',
          onOk: async (value: string) => {
            const res = await taxRateApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData, ctx) => {
        if (!data) {
          return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>Select a tax rate to view details.</div>
        }

        return (
          <TaxRateForm
            data={ data }
            currentLocale={ ctx?.currentLocale ?? 'en' }
            onChange={ (draft) => setData(draft) }
          />
        )
      } }
    />
  )
}