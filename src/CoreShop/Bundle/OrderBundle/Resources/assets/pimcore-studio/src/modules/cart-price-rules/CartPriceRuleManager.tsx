/**
 * CoreShop OrderBundle Studio Plugin
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
import { EntityTabbedManager, getErrorMessage, renderApiError } from '@coreshop/resource'
import { RuleForm, type RuleFormTab } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
import {
  ActionRegistry,
  ConditionRegistry,
  registerSchemaComponentsFromConfig,
  registerSchemaComponentsFromMaps
} from '@coreshop/rule/src/rules/registry'
import { useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { cartPriceRuleApi } from './api'
import type { CartPriceRule } from './types'
import { SettingsForm } from './components/SettingsForm'
import { VoucherCodesPanel } from './components/VoucherCodesPanel'
import { coreshopOrderServiceIds } from './service-ids'

type CartPriceRuleConfig = RuleConfig & {
  itemConditions?: string[]
  itemActions?: string[]
  itemConditionSchemaByType?: Record<string, string>
  itemActionSchemaByType?: Record<string, string>
}

export const CartPriceRuleManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const modal = useFormModal()
  const [config, setConfig] = React.useState<CartPriceRuleConfig>({ conditions: [], actions: [] })

  // Load config on mount
  React.useEffect(() => {
    cartPriceRuleApi.getConfig()
      .then((cfg: CartPriceRuleConfig) => {
        const conditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartPriceRuleConditionRegistry)
        const actionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartPriceRuleActionRegistry)
        const cartItemConditionRegistry = container.get<ConditionRegistry>(coreshopOrderServiceIds.cartItemConditionRegistry)
        const cartItemActionRegistry = container.get<ActionRegistry>(coreshopOrderServiceIds.cartItemActionRegistry)

        registerSchemaComponentsFromConfig(conditionRegistry, actionRegistry, cfg)
        registerSchemaComponentsFromMaps(
          cartItemConditionRegistry,
          cartItemActionRegistry,
          cfg.itemConditionSchemaByType,
          cfg.itemActionSchemaByType,
          cfg.schemas,
        )

        setConfig(cfg)
      })
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load config')))
      })
  }, [])

  return (
    <EntityTabbedManager<CartPriceRule>
      api={cartPriceRuleApi}
      dragType='coreshop:cart_price_rule'
      leftRootTitle={t('coreshop_cart_pricerules', { defaultValue: 'Cart Price Rules' })}
      localizable
      getTitle={(li, data) => data?.name ?? li?.name ?? `#${li?.id ?? ''}`}
      buildSavePayload={(data) => data}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: t('coreshop_cart_pricerule_add', { defaultValue: 'Add Cart Price Rule' }),
          label: t('coreshop_name', { defaultValue: 'Name' }),
          rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
          onOk: async (nameValue: string) => {
            const res = await cartPriceRuleApi.add({ name: nameValue })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData, ctx) => {
        if (!data) {
          return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
            {t('coreshop_cart_pricerule_select', { defaultValue: 'Select a cart price rule to view details.' })}
          </div>
        }

        const additionalTabs: RuleFormTab[] = [
          {
            key: 'voucher-codes',
            label: t('coreshop_cart_pricerule_voucherCodes', { defaultValue: 'Voucher Codes' }),
            disabled: !data.isVoucherRule,
            component: (
              <VoucherCodesPanel
                rule={data}
                disabled={!data.isVoucherRule}
              />
            )
          }
        ]

        return (
          <RuleForm
            rule={data}
            config={config}
            conditionRegistryId={coreshopOrderServiceIds.cartPriceRuleConditionRegistry}
            actionRegistryId={coreshopOrderServiceIds.cartPriceRuleActionRegistry}
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
            additionalTabs={additionalTabs}
            onChange={setData}
            hideToolbar={true}
          />
        )
      }}
    />
  )
}
