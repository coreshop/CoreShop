/**
 * CoreShop ProductBundle Studio Plugin
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
import type { ProductUnitDetail } from './api'
import { productUnitApi } from './api'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { ProductUnitForm } from './ProductUnitForm'
import { useTranslation } from 'react-i18next'

export const ProductUnitManager: React.FC = () => {
  const modal = useFormModal()
  const { t } = useTranslation()

  return (
    <EntityTabbedManager<ProductUnitDetail>
      api={productUnitApi}
      dragType='coreshop:product-unit'
      leftRootTitle={t('coreshop_product_units', { defaultValue: 'Product Units' })}
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () =>
        await new Promise<number>((resolve) => {
          modal.input({
            title: t('coreshop_product_unit_add', { defaultValue: 'Add Product Unit' }),
            label: t('coreshop_name', { defaultValue: 'Name' }),
            rule: {
              required: true,
              message: t('coreshop_name_required', { defaultValue: 'Name is required' })
            },
            onOk: async (nameValue: string) => {
              const res = await productUnitApi.add({ name: nameValue })
              resolve(res.data.id)
            }
          })
        })
      }
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>{t('coreshop_product_unit_select', { defaultValue: 'Select a product unit to view details.' })}</div>
        }

        // Merge current locale translations into top-level data
        const currentLocale = ctx?.currentLocale ?? 'en'
        const translationForLocale = data.translations?.[currentLocale]

        // If translation exists for locale, use it; otherwise use empty values
        const mergedData = {
          ...data,
          fullLabel: translationForLocale?.fullLabel ?? '',
          fullPluralLabel: translationForLocale?.fullPluralLabel ?? '',
          shortLabel: translationForLocale?.shortLabel ?? '',
          shortPluralLabel: translationForLocale?.shortPluralLabel ?? ''
        }

        return (
          <ProductUnitForm
            data={mergedData}
            onChange={(draft) => {
              // Merge changed fields back into translations structure
              const updatedTranslations = {
                ...(data.translations ?? {}),
                [currentLocale]: {
                  locale: currentLocale,
                  fullLabel: draft.fullLabel,
                  fullPluralLabel: draft.fullPluralLabel,
                  shortLabel: draft.shortLabel,
                  shortPluralLabel: draft.shortPluralLabel
                }
              }
              setData({
                ...draft,
                translations: updatedTranslations
              } as Partial<ProductUnitDetail>)
            }}
            currentLocale={currentLocale}
            locales={ctx?.locales}
          />
        )
      }}
    />
  )
}
