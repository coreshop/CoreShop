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
import { Tabs, Modal, Input, Form, Switch, Select, InputNumber, Button, Typography, Space } from 'antd'
import { PlusOutlined, SettingOutlined, SearchOutlined, TagOutlined } from '@ant-design/icons'
import { container } from '@pimcore/studio-ui-bundle'
import { useGlobalDataObjectContext } from '@pimcore/studio-ui-bundle/modules/data-object'
import { ConditionsPanel } from '@coreshop/rule/src/rules/components/ConditionsPanel'
import { ConditionRegistry } from '@coreshop/rule/src/rules/registry'
import { createSchemaCondition, EmptyCondition } from '@coreshop/rule/src/rules/components'
import type { RuleCondition } from '@coreshop/rule/src/rules/types'
import { useTranslation } from 'react-i18next'
import type { QuantityPriceRule, QuantityPriceRulesFieldData, CalculationBehaviour, QuantityRange } from '../types'
import { coreshopQuantityPriceRulesServiceIds } from '../service-ids'
import { RangesPanel } from './RangesPanel'

interface Props {
  value: QuantityPriceRulesFieldData
  onChange: (value: QuantityPriceRulesFieldData) => void
  disabled?: boolean
  currentLocale?: string
  locales?: string[]
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
 * Generate tab label with name, priority, and active status
 */
const generateTabLabel = (rule: QuantityPriceRule, t: (key: string, opts?: any) => string): React.ReactNode => {
  return (
    <Space size={4}>
      <TagOutlined style={{ color: '#ff6600' }} />
      <span>{rule.name || t('coreshop_new_rule', { defaultValue: 'New Rule' })}</span>
      {rule.priority !== undefined && rule.priority > 0 && (
        <Typography.Text type="secondary" style={{ fontSize: 11 }}>
          (Prio: {rule.priority})
        </Typography.Text>
      )}
      {!rule.active && (
        <span style={{
          display: 'inline-block',
          width: 8,
          height: 8,
          backgroundColor: '#ff4d4f',
          borderRadius: '50%',
          marginLeft: 4
        }} title={t('inactive', { defaultValue: 'Inactive' })} />
      )}
    </Space>
  )
}

export const ProductQuantityPriceRulesPanel: React.FC<Props> = ({
  value,
  onChange,
  disabled = false
}) => {
  const { t } = useTranslation()

  // Get objectId from Pimcore's global data object context
  const { context } = useGlobalDataObjectContext()
  const objectId = context?.config?.id

  const [activeRuleKey, setActiveRuleKey] = React.useState<string | undefined>(
    value.rules.length > 0 ? '0' : undefined
  )
  const [addModalVisible, setAddModalVisible] = React.useState(false)
  const [newRuleName, setNewRuleName] = React.useState('')

  // Check if condition registry is available
  const hasConditionRegistry = React.useMemo(() => {
    try {
      return container.isBound(coreshopQuantityPriceRulesServiceIds.conditionRegistry)
    } catch (e) {
      console.warn('Quantity price rules condition registry not available:', e)
      return false
    }
  }, [])

  // Get available condition types from backend (value.conditions)
  const availableConditionTypes = React.useMemo(() => {
    return value.conditions || []
  }, [value.conditions])

  React.useEffect(() => {
    if (!hasConditionRegistry) {
      return
    }

    const conditionRegistry = container.get<ConditionRegistry>(coreshopQuantityPriceRulesServiceIds.conditionRegistry)

    for (const [type, blockPrefix] of Object.entries(value.conditionSchemaByType ?? {})) {
      if (!conditionRegistry.has(type)) {
        conditionRegistry.register(type, createSchemaCondition(blockPrefix))
      }
    }

    // Known types without a schema mapping have no configuration form at all. They are
    // still valid, so render a neutral placeholder instead of "Unknown condition type".
    for (const type of availableConditionTypes) {
      if (!conditionRegistry.has(type)) {
        conditionRegistry.register(type, EmptyCondition)
      }
    }
  }, [hasConditionRegistry, value.conditionSchemaByType, availableConditionTypes])

  // Get calculation behaviour options
  const calculationBehaviourOptions = (value.stores?.calculationBehaviourTypes ?? DEFAULT_CALCULATION_BEHAVIOURS)
    .map(([val, labelKey]) => ({
      value: val,
      label: t(labelKey, { defaultValue: labelKey.replace(/coreshop_product_quantity_price_rules_calculation_behaviour_/g, '') })
    }))

  const handleRuleChange = (index: number, updatedRule: QuantityPriceRule) => {
    const newRules = [...value.rules]
    newRules[index] = updatedRule
    onChange({ ...value, rules: newRules })
  }

  const handleFieldChange = (index: number, field: keyof QuantityPriceRule, fieldValue: any) => {
    const rule = value.rules[index]
    if (!rule) return
    handleRuleChange(index, { ...rule, [field]: fieldValue })
  }

  const handleAddRule = () => {
    if (!newRuleName.trim()) return
    // Create a default range to satisfy backend validation (min: 1 range required)
    // Use 'percentage_decrease' as default since 'fixed' requires a currency
    const defaultRange: QuantityRange = {
      id: null,
      rangeStartingFrom: 0,
      pricingBehaviour: 'percentage_decrease',
      amount: 0,
      percentage: 0,
      pseudoPrice: 0,
      currency: null,
      highlighted: false
    }
    const newRule: QuantityPriceRule = {
      name: newRuleName,
      // Use 'volume' - the only registered calculator type in the backend
      calculationBehaviour: 'volume',
      priority: 0,
      active: true,
      conditions: [],
      ranges: [defaultRange]
    }
    const newRules = [...value.rules, newRule]
    onChange({ ...value, rules: newRules })
    setActiveRuleKey(String(newRules.length - 1))
    setNewRuleName('')
    setAddModalVisible(false)
  }

  const handleDeleteRule = (index: number) => {
    Modal.confirm({
      title: t('coreshop_delete_rule', { defaultValue: 'Delete Rule?' }),
      content: t('coreshop_delete_rule_confirm', {
        defaultValue: `Are you sure you want to delete "${value.rules[index]?.name}"?`,
        name: value.rules[index]?.name
      }),
      onOk: () => {
        const newRules = value.rules.filter((_, i) => i !== index)
        onChange({ ...value, rules: newRules })

        // Adjust active key
        if (activeRuleKey === String(index)) {
          setActiveRuleKey(newRules.length > 0 ? '0' : undefined)
        } else if (Number(activeRuleKey) > index) {
          setActiveRuleKey(String(Number(activeRuleKey) - 1))
        }
      }
    })
  }

  // Build sub-tabs for a single rule
  const buildRuleSubTabs = (rule: QuantityPriceRule, ruleIndex: number) => {
    return [
      {
        key: 'settings',
        label: (
          <Space size={4}>
            <SettingOutlined />
            {t('settings', { defaultValue: 'Settings' })}
          </Space>
        ),
        children: (
          <div style={{ padding: 16 }}>
            <table style={{ borderCollapse: 'separate', borderSpacing: '8px 12px' }}>
              <tbody>
                <tr>
                  <td style={{ textAlign: 'right', paddingRight: 8, whiteSpace: 'nowrap' }}>
                    {t('name', { defaultValue: 'Name' })}:
                  </td>
                  <td>
                    <Input
                      value={rule.name}
                      onChange={(e) => handleFieldChange(ruleIndex, 'name', e.target.value)}
                      style={{ width: 200 }}
                      disabled={disabled}
                    />
                  </td>
                </tr>
                <tr>
                  <td style={{ textAlign: 'right', paddingRight: 8, whiteSpace: 'nowrap' }}>
                    {t('coreshop_product_quantity_price_rules_calculation_behaviour', { defaultValue: 'Calculation Behaviour' })}:
                  </td>
                  <td>
                    <Select
                      value={rule.calculationBehaviour}
                      options={calculationBehaviourOptions}
                      onChange={(val: CalculationBehaviour) => handleFieldChange(ruleIndex, 'calculationBehaviour', val)}
                      style={{ width: 200 }}
                      disabled={disabled}
                    />
                  </td>
                </tr>
                <tr>
                  <td style={{ textAlign: 'right', paddingRight: 8, whiteSpace: 'nowrap' }}>
                    {t('coreshop_priority', { defaultValue: 'Priority' })}:
                  </td>
                  <td>
                    <InputNumber
                      value={rule.priority}
                      onChange={(val) => handleFieldChange(ruleIndex, 'priority', val ?? 0)}
                      style={{ width: 200 }}
                      disabled={disabled}
                    />
                  </td>
                </tr>
                <tr>
                  <td style={{ textAlign: 'right', paddingRight: 8, whiteSpace: 'nowrap' }}>
                    {t('active', { defaultValue: 'Active' })}:
                  </td>
                  <td>
                    <Switch
                      checked={rule.active}
                      onChange={(checked) => handleFieldChange(ruleIndex, 'active', checked)}
                      disabled={disabled}
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        )
      },
      {
        key: 'conditions',
        label: (
          <Space size={4}>
            <SearchOutlined />
            {t('coreshop_conditions', { defaultValue: 'Conditions' })}
          </Space>
        ),
        children: hasConditionRegistry ? (
          <div style={{ padding: 16 }}>
            <ConditionsPanel
              conditions={rule.conditions}
              availableTypes={availableConditionTypes}
              onChange={(conditions: RuleCondition[]) => handleFieldChange(ruleIndex, 'conditions', conditions)}
              registryId={coreshopQuantityPriceRulesServiceIds.conditionRegistry}
            />
          </div>
        ) : (
          <div style={{ padding: 16 }}>
            <Typography.Text type="secondary">
              {t('coreshop_conditions_not_available', { defaultValue: 'Conditions not available' })}
            </Typography.Text>
          </div>
        )
      },
      {
        key: 'ranges',
        label: (
          <Space size={4}>
            <TagOutlined />
            {t('coreshop_product_quantity_price_rules_ranges', { defaultValue: 'Quantity Price Ranges' })}
          </Space>
        ),
        children: (
          <div style={{ padding: 16 }}>
            <RangesPanel
              ranges={rule.ranges}
              onChange={(ranges: QuantityRange[]) => handleFieldChange(ruleIndex, 'ranges', ranges)}
              pricingBehaviourTypes={value.stores?.pricingBehaviourTypes}
              disabled={disabled}
              objectId={objectId}
            />
          </div>
        )
      }
    ]
  }

  // Build main rule tabs
  const ruleTabItems = value.rules.map((rule, index) => ({
    key: String(index),
    label: generateTabLabel(rule, t),
    closable: !disabled,
    children: (
      <Tabs
        defaultActiveKey="settings"
        items={buildRuleSubTabs(rule, index)}
        size="small"
      />
    )
  }))

  // Handle tab edit (close)
  const onTabEdit = (targetKey: React.MouseEvent | React.KeyboardEvent | string, action: 'add' | 'remove') => {
    if (action === 'remove' && typeof targetKey === 'string') {
      handleDeleteRule(Number(targetKey))
    }
  }

  return (
    <div style={{ minHeight: 400 }}>
      {/* Header with title and add button */}
      <div style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 8,
        padding: '8px 0'
      }}>
        <Typography.Text strong>
          {t('coreshop_product_quantity_price_rules', { defaultValue: 'Quantity Price Rules' })}
        </Typography.Text>
        {!disabled && (
          <Button
            type="primary"
            size="small"
            icon={<PlusOutlined />}
            onClick={() => setAddModalVisible(true)}
          />
        )}
      </div>

      {/* Rule tabs */}
      {value.rules.length > 0 ? (
        <Tabs
          type="editable-card"
          activeKey={activeRuleKey}
          onChange={setActiveRuleKey}
          onEdit={onTabEdit}
          items={ruleTabItems}
          hideAdd
          size="small"
        />
      ) : (
        <div style={{
          padding: 40,
          textAlign: 'center',
          background: '#fafafa',
          border: '1px dashed #d9d9d9',
          borderRadius: 4
        }}>
          <Typography.Text type="secondary">
            {t('coreshop_no_quantity_price_rules', { defaultValue: 'No quantity price rules defined' })}
          </Typography.Text>
          {!disabled && (
            <div style={{ marginTop: 16 }}>
              <Button
                type="primary"
                icon={<PlusOutlined />}
                onClick={() => setAddModalVisible(true)}
              >
                {t('coreshop_add_quantity_price_rule', { defaultValue: 'Add Quantity Price Rule' })}
              </Button>
            </div>
          )}
        </div>
      )}

      {/* Add rule modal */}
      <Modal
        title={t('coreshop_add_quantity_price_rule', { defaultValue: 'Add Quantity Price Rule' })}
        open={addModalVisible}
        onOk={handleAddRule}
        onCancel={() => {
          setAddModalVisible(false)
          setNewRuleName('')
        }}
        okButtonProps={{ disabled: !newRuleName.trim() }}
      >
        <Form layout="vertical">
          <Form.Item
            label={t('coreshop_name', { defaultValue: 'Name' })}
            required
          >
            <Input
              value={newRuleName}
              onChange={(e) => setNewRuleName(e.target.value)}
              placeholder={t('coreshop_enter_rule_name', { defaultValue: 'Enter rule name' })}
              onPressEnter={() => newRuleName.trim() && handleAddRule()}
              autoFocus
            />
          </Form.Item>
        </Form>
      </Modal>
    </div>
  )
}
