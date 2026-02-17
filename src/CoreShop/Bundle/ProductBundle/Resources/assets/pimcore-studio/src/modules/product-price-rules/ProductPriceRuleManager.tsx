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
import { container } from '@pimcore/studio-ui-bundle'
import { EntityTabbedManager } from '@coreshop/resource'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import { ActionRegistry, ConditionRegistry, registerSchemaComponentsFromConfig } from '@coreshop/rule/src/rules/registry'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { productPriceRuleApi } from './api'
import type { ProductPriceRule } from './types'
import { SettingsForm } from './components/SettingsForm'
import { coreshopProductServiceIds } from './service-ids'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

export const ProductPriceRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const modal = useFormModal()
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    productPriceRuleApi.getConfig()
      .then((cfg) => {
        const conditionRegistry = container.get<ConditionRegistry>(coreshopProductServiceIds.productPriceRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopProductServiceIds.productPriceRuleActionRegistry)
        registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, cfg)
        setConfig(cfg)
      })
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load config')))
      })
  }, [])

  return (
    <EntityTabbedManager<ProductPriceRule>
      api={productPriceRuleApi}
      dragType='coreshop:product_price_rule'
      leftRootTitle={t('coreshop.product.product_specific_price_rules', { defaultValue: 'Product Price Rules' })}
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop.product.product_specific_price_rules', { defaultValue: 'Add Product Price Rule' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await productPriceRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            {t('coreshop_product_price_rule_select', { defaultValue: 'Select a product price rule to view details.' })}
          </div>
        }

        return (
          <RuleForm
            rule={data}
            config={config}
            conditionRegistryId={coreshopProductServiceIds.productPriceRuleConditionRegistry}
            actionRegistryId={coreshopProductServiceIds.productPriceRuleActionRegistry}
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
