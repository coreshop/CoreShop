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
import { Tabs, Modal, Input, Form, Button, Typography, Space } from 'antd'
import { PlusOutlined, TagOutlined } from '@ant-design/icons'
import { container } from '@pimcore/studio-ui-bundle'
import type { RuleConfig } from '@coreshop/rule/src/rules/types'
import { RuleForm } from '@coreshop/rule/src/rules/components/RuleForm'
import {
  ActionRegistry,
  ConditionRegistry,
  registerSchemaComponentsFromMaps
} from '@coreshop/rule/src/rules/registry'
import { useTranslation } from 'react-i18next'
import type { ProductSpecificPriceRule, ProductSpecificPriceRulesData } from '../types'
import { coreshopProductServiceIds } from '../../product-price-rules/service-ids'
import { SettingsForm } from './SettingsForm'

interface Props {
  value: ProductSpecificPriceRulesData
  onChange: (value: ProductSpecificPriceRulesData) => void
  disabled?: boolean
  currentLocale?: string
  locales?: string[]
}

/**
 * Generate tab label with name, priority, and active status
 */
const generateTabLabel = (rule: ProductSpecificPriceRule, t: (key: string, opts?: any) => string): React.ReactNode => {
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

export const ProductSpecificPriceRulesPanel: React.FC<Props> = ({
  value,
  onChange,
  disabled = false,
  currentLocale = 'en',
  locales = ['en', 'de']
}) => {
  const { t } = useTranslation()
  const [activeRuleKey, setActiveRuleKey] = React.useState<string | undefined>(
    value.rules.length > 0 ? '0' : undefined
  )
  const [addModalVisible, setAddModalVisible] = React.useState(false)
  const [newRuleName, setNewRuleName] = React.useState('')

  // Register schema-based components from backend mappings
  React.useEffect(() => {
    try {
      const conditionRegistry = container.get<ConditionRegistry>(coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry)
      const actionRegistry = container.get<ActionRegistry>(coreshopProductServiceIds.productSpecificPriceRuleActionRegistry)

      registerSchemaComponentsFromMaps(
        conditionRegistry,
        actionRegistry,
        value.conditionSchemaByType,
        value.actionSchemaByType,
      )
    } catch (e) {
      console.warn('Product specific price rules registries not available:', e)
    }
  }, [
    value.conditionSchemaByType,
    value.actionSchemaByType,
  ])

  // Build RuleConfig from the value prop
  const config: RuleConfig = React.useMemo(() => ({
    conditions: value.conditions || [],
    actions: value.actions || [],
    conditionSchemaByType: value.conditionSchemaByType,
    actionSchemaByType: value.actionSchemaByType,
  }), [value.conditions, value.actions, value.conditionSchemaByType, value.actionSchemaByType])

  const handleRuleChange = (index: number, updatedRule: ProductSpecificPriceRule) => {
    const newRules = [...value.rules]
    newRules[index] = updatedRule
    onChange({ ...value, rules: newRules })
  }

  const handleAddRule = () => {
    if (!newRuleName.trim()) return
    const newRule: ProductSpecificPriceRule = {
      name: newRuleName,
      active: true,
      priority: 0,
      inherit: false,
      conditions: [],
      actions: []
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

  // Build main rule tabs - each rule delegates to RuleForm
  const ruleTabItems = value.rules.map((rule, index) => ({
    key: String(index),
    label: generateTabLabel(rule, t),
    closable: !disabled,
    children: (
      <RuleForm
        rule={rule}
        config={config}
        conditionRegistryId={coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry}
        actionRegistryId={coreshopProductServiceIds.productSpecificPriceRuleActionRegistry}
        currentLocale={currentLocale}
        locales={locales}
        settingsComponent={
          <SettingsForm
            rule={rule}
            onChange={(updatedRule) => handleRuleChange(index, updatedRule)}
            currentLocale={currentLocale}
            locales={locales}
          />
        }
        onChange={(updatedRule) => handleRuleChange(index, updatedRule as ProductSpecificPriceRule)}
        hideToolbar
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
          {t('coreshop_product_specific_price_rules', { defaultValue: 'Product Specific Price Rules' })}
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
            {t('coreshop_no_specific_price_rules', { defaultValue: 'No price rules defined' })}
          </Typography.Text>
          {!disabled && (
            <div style={{ marginTop: 16 }}>
              <Button
                type="primary"
                icon={<PlusOutlined />}
                onClick={() => setAddModalVisible(true)}
              >
                {t('coreshop_add_price_rule', { defaultValue: 'Add Price Rule' })}
              </Button>
            </div>
          )}
        </div>
      )}

      {/* Add rule modal */}
      <Modal
        title={t('coreshop_add_price_rule', { defaultValue: 'Add Price Rule' })}
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
