/**
 * CoreShop ProductQuantityPriceRulesBundle Studio Plugin
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
import { Tabs, Form, Input, InputNumber, Switch, Select, Typography, Card } from 'antd'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { ConditionsPanel } from '@coreshop/rule/src/rules/components/ConditionsPanel'
import type { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import type { RuleCondition } from '@coreshop/rule/src/rules/types'
import { coreshopQuantityPriceRulesServiceIds } from '../service-ids'
import type { QuantityPriceRule, CalculationBehaviour, QuantityRange, QuantityPriceRuleStoreData } from '../types'
import { RangesPanel } from './RangesPanel'

interface QuantityPriceRulePanelProps {
  rule: QuantityPriceRule
  onChange: (rule: QuantityPriceRule) => void
  storeData?: QuantityPriceRuleStoreData
  disabled?: boolean
}

/**
 * Default calculation behaviour types
 */
const DEFAULT_CALCULATION_BEHAVIOURS: Array<[string, string]> = [
  ['by_quantity', 'coreshop_product_quantity_price_rules_calculation_behaviour_by_quantity'],
  ['by_percentage', 'coreshop_product_quantity_price_rules_calculation_behaviour_by_percentage'],
  ['by_price', 'coreshop_product_quantity_price_rules_calculation_behaviour_by_price']
]

/**
 * QuantityPriceRulePanel - Complete panel for editing a single quantity price rule
 */
export const QuantityPriceRulePanel: React.FC<QuantityPriceRulePanelProps> = ({
  rule,
  onChange,
  storeData,
  disabled = false
}) => {
  const { t } = useTranslation()

  // Get calculation behaviour options
  const calculationBehaviourOptions = (storeData?.calculationBehaviourTypes ?? DEFAULT_CALCULATION_BEHAVIOURS)
    .map(([value, labelKey]) => ({
      value,
      label: t(labelKey, { defaultValue: labelKey.replace(/coreshop_product_quantity_price_rules_calculation_behaviour_/g, '') })
    }))

  // Get condition registry
  const conditionRegistry = React.useMemo(() => {
    try {
      if (container.isBound(coreshopQuantityPriceRulesServiceIds.conditionRegistry)) {
        return container.get<ConditionRegistry>(coreshopQuantityPriceRulesServiceIds.conditionRegistry)
      }
    } catch (e) {
      console.warn('Quantity price rules condition registry not available:', e)
    }
    return null
  }, [])

  // Handle field changes
  const handleFieldChange = <K extends keyof QuantityPriceRule>(field: K, value: QuantityPriceRule[K]) => {
    onChange({ ...rule, [field]: value })
  }

  // Settings tab content
  const settingsContent = (
    <Form layout="vertical" disabled={disabled}>
      <Form.Item label={t('name', { defaultValue: 'Name' })} required>
        <Input
          value={rule.name}
          onChange={(e) => handleFieldChange('name', e.target.value)}
          placeholder={t('coreshop_enter_name', { defaultValue: 'Enter name' })}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_product_quantity_price_rules_calculation_behaviour', { defaultValue: 'Calculation Behaviour' })}>
        <Select
          value={rule.calculationBehaviour}
          options={calculationBehaviourOptions}
          onChange={(value: CalculationBehaviour) => handleFieldChange('calculationBehaviour', value)}
          style={{ width: 250 }}
        />
      </Form.Item>

      <Form.Item label={t('coreshop_priority', { defaultValue: 'Priority' })}>
        <InputNumber
          value={rule.priority}
          onChange={(value) => handleFieldChange('priority', value ?? 0)}
          style={{ width: 250 }}
        />
      </Form.Item>

      <Form.Item label={t('active', { defaultValue: 'Active' })} valuePropName="checked">
        <Switch
          checked={rule.active}
          onChange={(checked) => handleFieldChange('active', checked)}
        />
      </Form.Item>
    </Form>
  )

  // Get available condition types from registry
  const availableConditionTypes = React.useMemo(() => {
    if (!conditionRegistry) return []
    return Array.from(conditionRegistry.getAll().keys())
  }, [conditionRegistry])

  // Conditions tab content
  const conditionsContent = conditionRegistry ? (
    <ConditionsPanel
      conditions={rule.conditions}
      availableTypes={availableConditionTypes}
      onChange={(conditions: RuleCondition[]) => handleFieldChange('conditions', conditions)}
      registryId={coreshopQuantityPriceRulesServiceIds.conditionRegistry}
    />
  ) : (
    <Typography.Text type="secondary">
      {t('coreshop_conditions_registry_not_available', { defaultValue: 'Conditions registry not available' })}
    </Typography.Text>
  )

  // Ranges tab content
  const rangesContent = (
    <RangesPanel
      ranges={rule.ranges}
      onChange={(ranges: QuantityRange[]) => handleFieldChange('ranges', ranges)}
      pricingBehaviourTypes={storeData?.pricingBehaviourTypes}
      disabled={disabled}
    />
  )

  // Tab items
  const tabItems = [
    {
      key: 'settings',
      label: t('settings', { defaultValue: 'Settings' }),
      children: settingsContent
    },
    {
      key: 'conditions',
      label: t('coreshop_conditions', { defaultValue: 'Conditions' }),
      children: conditionsContent
    },
    {
      key: 'ranges',
      label: t('coreshop_product_quantity_price_rules_ranges', { defaultValue: 'Ranges' }),
      children: rangesContent
    }
  ]

  return (
    <Card
      title={
        <span>
          {rule.name || t('coreshop_new_rule', { defaultValue: 'New Rule' })}
          {!rule.active && (
            <Typography.Text type="secondary" style={{ marginLeft: 8, fontSize: 12 }}>
              ({t('inactive', { defaultValue: 'Inactive' })})
            </Typography.Text>
          )}
          <Typography.Text type="secondary" style={{ marginLeft: 8, fontSize: 12 }}>
            (Prio: {rule.priority})
          </Typography.Text>
        </span>
      }
      size="small"
    >
      <Tabs
        defaultActiveKey="settings"
        items={tabItems}
        size="small"
      />
    </Card>
  )
}
