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
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { productPriceRuleApi } from './api'
import type { ProductPriceRule } from './types'
import { SettingsForm } from './components/SettingsForm'
import { coreshopProductServiceIds } from './service-ids'

export const ProductPriceRuleManager: React.FC = () => {
  const modal = useFormModal()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    productPriceRuleApi.getConfig()
      .then(setConfig)
      .catch(err => {
        console.error('Failed to load config:', err)
      })
  }, [])

  return (
    <EntityTabbedManager<ProductPriceRule>
      api={productPriceRuleApi}
      dragType='coreshop:product_price_rule'
      leftRootTitle='Product Price Rules'
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => {
        const translations: Record<string, { label: string }> = {}
        const rawTranslations = (data.translations ?? {}) as Record<string, any>
        Object.keys(rawTranslations).forEach((locale) => {
          const entry = rawTranslations[locale] ?? {}
          translations[locale] = { label: entry?.label ?? '' }
        })

        return {
          id: data.id,
          name: data.name,
          description: data.description,
          active: data.active,
          priority: data.priority,
          conditions: data.conditions,
          actions: data.actions,
          translations
        }
      }}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: 'Add Product Price Rule',
          label: 'Name',
          rule: { required: true, message: 'Name is required' },
          onOk: async (nameValue: string) => {
            const res = await productPriceRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            Select a product price rule to view details.
          </div>
        }

        return (
          <RuleForm
            rule={data}
            config={config}
            conditionRegistryId={coreshopProductServiceIds.productPriceRuleConditionRegistry}
            actionRegistryId={coreshopProductServiceIds.productPriceRuleActionRegistry}
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
