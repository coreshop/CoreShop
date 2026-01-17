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
import { Tree, Modal, Input, Form, Empty, Button } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import type { DataNode } from 'antd/es/tree'
import {
  SplitLayout,
  Content,
  ContentLayout,
  Toolbar as PimToolbar,
  Dropdown,
  DropdownButton,
  Icon,
  IconButton
} from '@pimcore/studio-ui-bundle/components'
import { RuleForm } from '@coreshop/rule/src/rules'
import type { RuleConfig } from '@coreshop/rule/src/rules'
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

export const ProductSpecificPriceRulesPanel: React.FC<Props> = ({
  value,
  onChange,
  disabled = false,
  currentLocale = 'en',
  locales = ['en', 'de']
}) => {
  const { t } = useTranslation()
  const [selectedIndex, setSelectedIndex] = React.useState<number | null>(null)
  const [addModalVisible, setAddModalVisible] = React.useState(false)
  const [newRuleName, setNewRuleName] = React.useState('')

  const config: RuleConfig = React.useMemo(() => ({
    conditions: value.conditions || [],
    actions: value.actions || []
  }), [value.conditions, value.actions])

  const selectedRule = selectedIndex !== null ? value.rules[selectedIndex] : undefined

  const handleRuleChange = (updatedRule: ProductSpecificPriceRule) => {
    if (selectedIndex === null) return
    const newRules = [...value.rules]
    newRules[selectedIndex] = updatedRule
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
    setSelectedIndex(newRules.length - 1)
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
        if (selectedIndex === index) {
          setSelectedIndex(newRules.length > 0 ? 0 : null)
        } else if (selectedIndex !== null && selectedIndex > index) {
          setSelectedIndex(selectedIndex - 1)
        }
      }
    })
  }

  // Build tree data for the left panel
  const treeData: DataNode[] = React.useMemo(() => {
    const children: DataNode[] = value.rules.map((rule, index) => ({
      key: index,
      title: (
        <Dropdown
          trigger={['contextMenu']}
          menu={{
            items: [
              {
                key: 'delete',
                icon: <Icon value='trash' />,
                label: t('toolbar.delete', { defaultValue: 'Delete' }),
                onClick: () => handleDeleteRule(index),
                disabled
              }
            ]
          }}
        >
          <span>{rule.name || `${t('coreshop_rule', { defaultValue: 'Rule' })} ${index + 1}`}</span>
        </Dropdown>
      ),
      isLeaf: true
    }))

    return [{
      key: 'root',
      title: (
        <>
          <Icon value='folder' />
          <span style={{ marginLeft: 4 }}>
            {t('coreshop_product_specific_price_rules', { defaultValue: 'Product Specific Price Rules' })}
          </span>
        </>
      ),
      selectable: false,
      children
    }]
  }, [value.rules, t, disabled])

  const [expandedKeys, setExpandedKeys] = React.useState<React.Key[]>(['root'])

  // Left panel with tree
  const leftPanel = {
    id: 'rule-list',
    size: 25,
    minSize: 200,
    children: [
      <ContentLayout
        key="rule-list-layout"
        renderToolbar={
          <PimToolbar>
            {!disabled && (
              <Dropdown
                menu={{
                  items: [{
                    key: 'add',
                    label: t('coreshop_add_rule', { defaultValue: 'New Rule' }),
                    icon: <Icon value='new' />,
                    onClick: () => setAddModalVisible(true)
                  }]
                }}
                trigger={['click']}
              >
                <DropdownButton>
                  {t('toolbar.new', { defaultValue: 'New' })}
                </DropdownButton>
              </Dropdown>
            )}
          </PimToolbar>
        }
      >
        <Tree
          treeData={treeData}
          expandedKeys={expandedKeys}
          onExpand={(keys) => setExpandedKeys(keys as React.Key[])}
          selectedKeys={selectedIndex !== null ? [selectedIndex] : []}
          onSelect={(keys) => {
            const key = Array.isArray(keys) ? keys[0] : keys
            if (typeof key === 'number') {
              setSelectedIndex(key)
            }
          }}
          style={{ padding: 8 }}
        />
      </ContentLayout>
    ]
  }

  // Right panel with rule details
  const rightPanel = {
    id: 'rule-detail',
    size: 75,
    minSize: 400,
    children: [
      <Content key="rule-detail-content" style={{ height: '100%', overflow: 'auto' }}>
        {selectedRule ? (
          <RuleForm
            rule={selectedRule}
            config={config}
            conditionRegistryId={coreshopProductServiceIds.productSpecificPriceRuleConditionRegistry}
            actionRegistryId={coreshopProductServiceIds.productSpecificPriceRuleActionRegistry}
            settingsComponent={
              <SettingsForm
                rule={selectedRule}
                onChange={handleRuleChange}
                currentLocale={currentLocale}
                locales={locales}
              />
            }
            onChange={(r) => handleRuleChange(r as ProductSpecificPriceRule)}
            hideToolbar
          />
        ) : (
          <Empty
            style={{ marginTop: 100 }}
            description={
              value.rules.length === 0
                ? t('coreshop_no_specific_price_rules', { defaultValue: 'No price rules defined' })
                : t('coreshop_select_rule', { defaultValue: 'Select a rule to view details' })
            }
          >
            {!disabled && value.rules.length === 0 && (
              <Button
                type="primary"
                icon={<PlusOutlined />}
                onClick={() => setAddModalVisible(true)}
              >
                {t('coreshop_add_price_rule', { defaultValue: 'Add Price Rule' })}
              </Button>
            )}
          </Empty>
        )}
      </Content>
    ]
  }

  return (
    <div style={{ height: 500, display: 'flex', flexDirection: 'column' }}>
      <SplitLayout
        leftItem={leftPanel}
        rightItem={rightPanel}
        withDivider
      />

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
