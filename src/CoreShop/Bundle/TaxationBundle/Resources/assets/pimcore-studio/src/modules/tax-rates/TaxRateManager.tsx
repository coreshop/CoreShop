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
import { useTranslation } from 'react-i18next'
import { taxRateApi, type TaxRateDetail } from './api'
import { TaxRateForm } from './TaxRateForm'

export const TaxRateManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()

  return (
    <EntityTabbedManager<TaxRateDetail>
      api={ taxRateApi }
      dragType='coreshop:tax_rate'
      leftRootTitle={t('coreshop_tax_rate', { defaultValue: 'Tax Rates' })}
      localizable
      getTitle={ (li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}` }
      buildSavePayload={ (data) => data }
      onAdd={ async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_tax_rate', { defaultValue: 'Add Tax Rate' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          onOk: async (value: string) => {
            const res = await taxRateApi.add({ name: value })
            resolve(res.data.id)
          }
        })
      }) }
      renderDetail={ (data, setData, ctx) => {
        if (!data) {
          return <div style={ { padding: 12, color: 'var(--ant-color-text-tertiary)' } }>{t('coreshop_tax_rate_select', { defaultValue: 'Select a tax rate to view details.' })}</div>
        }

        return (
          <TaxRateForm
            data={ data }
            currentLocale={ ctx?.currentLocale ?? 'en' }
            locales={ ctx?.locales }
            onChange={ (draft) => setData(draft) }
          />
        )
      } }
    />
  )
}