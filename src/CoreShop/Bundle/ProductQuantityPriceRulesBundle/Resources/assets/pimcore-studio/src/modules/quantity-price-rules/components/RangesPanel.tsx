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
import { Table, Button, Space, InputNumber, Select, Checkbox, Typography } from 'antd'
import { PlusOutlined, DeleteOutlined, ArrowUpOutlined, ArrowDownOutlined, CopyOutlined, SnippetsOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { QuantityRange, PricingBehaviour } from '../types'

interface RangesPanelProps {
  ranges: QuantityRange[]
  onChange: (ranges: QuantityRange[]) => void
  pricingBehaviourTypes?: Array<[string, string]>
  disabled?: boolean
}

/**
 * Default pricing behaviour types
 */
const DEFAULT_PRICING_BEHAVIOURS: Array<[string, string]> = [
  ['fixed', 'coreshop_product_quantity_price_rules_behaviour_fixed'],
  ['percentage_decrease', 'coreshop_product_quantity_price_rules_behaviour_percentage_decrease'],
  ['percentage_increase', 'coreshop_product_quantity_price_rules_behaviour_percentage_increase'],
  ['amount_decrease', 'coreshop_product_quantity_price_rules_behaviour_amount_decrease'],
  ['amount_increase', 'coreshop_product_quantity_price_rules_behaviour_amount_increase']
]

/**
 * Generate a unique temporary ID for new ranges
 */
let tempIdCounter = 0
const generateTempId = (): string => `temp_${++tempIdCounter}`

/**
 * RangesPanel - Editable grid for quantity price ranges
 */
export const RangesPanel: React.FC<RangesPanelProps> = ({
  ranges,
  onChange,
  pricingBehaviourTypes = DEFAULT_PRICING_BEHAVIOURS,
  disabled = false
}) => {
  const { t } = useTranslation()
  const [clipboard, setClipboard] = React.useState<QuantityRange[] | null>(null)

  // Add a key to each range for React rendering
  const rangesWithKeys = React.useMemo(() =>
    ranges.map((range, index) => ({
      ...range,
      key: range.id ?? `new_${index}`
    }))
  , [ranges])

  // Pricing behaviour options for select
  const pricingOptions = pricingBehaviourTypes.map(([value, labelKey]) => ({
    value,
    label: t(labelKey, { defaultValue: labelKey.replace(/coreshop_product_quantity_price_rules_behaviour_/g, '') })
  }))

  // Handle adding a new range
  const handleAdd = () => {
    const lastRange = ranges[ranges.length - 1]
    const newRange: QuantityRange = {
      id: null,
      rangeStartingFrom: lastRange ? lastRange.rangeStartingFrom + 10 : 0,
      pricingBehaviour: 'fixed',
      highlighted: false
    }
    onChange([...ranges, newRange])
  }

  // Handle updating a range
  const handleUpdate = (index: number, field: keyof QuantityRange, value: any) => {
    const newRanges = [...ranges]
    newRanges[index] = { ...newRanges[index], [field]: value }
    onChange(newRanges)
  }

  // Handle deleting a range
  const handleDelete = (index: number) => {
    const newRanges = ranges.filter((_, i) => i !== index)
    onChange(newRanges)
  }

  // Handle moving a range up
  const handleMoveUp = (index: number) => {
    if (index === 0) return
    const newRanges = [...ranges]
    const temp = newRanges[index]
    newRanges[index] = newRanges[index - 1]
    newRanges[index - 1] = temp
    onChange(newRanges)
  }

  // Handle moving a range down
  const handleMoveDown = (index: number) => {
    if (index === ranges.length - 1) return
    const newRanges = [...ranges]
    const temp = newRanges[index]
    newRanges[index] = newRanges[index + 1]
    newRanges[index + 1] = temp
    onChange(newRanges)
  }

  // Handle copying ranges to clipboard
  const handleCopy = () => {
    setClipboard(ranges.map(r => ({ ...r, id: null })))
  }

  // Handle pasting ranges from clipboard
  const handlePaste = () => {
    if (clipboard && clipboard.length > 0) {
      const newRanges = clipboard.map(r => ({ ...r, id: null }))
      onChange([...ranges, ...newRanges])
    }
  }

  // Table columns
  const columns = [
    {
      title: t('coreshop_product_quantity_price_rules_range_starting_from', { defaultValue: 'Starting From (Qty)' }),
      dataIndex: 'rangeStartingFrom',
      key: 'rangeStartingFrom',
      width: 180,
      render: (value: number, _record: QuantityRange, index: number) => (
        <InputNumber
          value={value}
          min={0}
          precision={0}
          disabled={disabled}
          style={{ width: '100%' }}
          onChange={(val) => handleUpdate(index, 'rangeStartingFrom', val ?? 0)}
          addonAfter={t('coreshop_product_quantity_price_rules_quantity_amount', { defaultValue: 'pcs' })}
        />
      )
    },
    {
      title: t('coreshop_product_quantity_price_rules_behaviour', { defaultValue: 'Pricing Behaviour' }),
      dataIndex: 'pricingBehaviour',
      key: 'pricingBehaviour',
      width: 200,
      render: (value: PricingBehaviour, _record: QuantityRange, index: number) => (
        <Select
          value={value}
          options={pricingOptions}
          disabled={disabled}
          style={{ width: '100%' }}
          onChange={(val) => handleUpdate(index, 'pricingBehaviour', val)}
        />
      )
    },
    {
      title: t('coreshop_product_quantity_price_rules_highlight', { defaultValue: 'Highlight' }),
      dataIndex: 'highlighted',
      key: 'highlighted',
      width: 100,
      render: (value: boolean, _record: QuantityRange, index: number) => (
        <Checkbox
          checked={value}
          disabled={disabled}
          onChange={(e) => handleUpdate(index, 'highlighted', e.target.checked)}
        />
      )
    },
    {
      title: t('actions', { defaultValue: 'Actions' }),
      key: 'actions',
      width: 120,
      render: (_: any, _record: QuantityRange, index: number) => (
        <Space size="small">
          <Button
            type="text"
            size="small"
            icon={<ArrowUpOutlined />}
            disabled={disabled || index === 0}
            onClick={() => handleMoveUp(index)}
          />
          <Button
            type="text"
            size="small"
            icon={<ArrowDownOutlined />}
            disabled={disabled || index === ranges.length - 1}
            onClick={() => handleMoveDown(index)}
          />
          <Button
            type="text"
            size="small"
            danger
            icon={<DeleteOutlined />}
            disabled={disabled}
            onClick={() => handleDelete(index)}
          />
        </Space>
      )
    }
  ]

  return (
    <div>
      <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Typography.Text strong>
          {t('coreshop_product_quantity_price_rules_ranges', { defaultValue: 'Quantity Ranges' })}
        </Typography.Text>
        <Space>
          <Button
            size="small"
            icon={<PlusOutlined />}
            onClick={handleAdd}
            disabled={disabled}
          >
            {t('add', { defaultValue: 'Add' })}
          </Button>
          <Button
            size="small"
            icon={<CopyOutlined />}
            onClick={handleCopy}
            disabled={disabled || ranges.length === 0}
          >
            {t('coreshop_product_quantity_price_rules_copy_ranges', { defaultValue: 'Copy' })}
          </Button>
          <Button
            size="small"
            icon={<SnippetsOutlined />}
            onClick={handlePaste}
            disabled={disabled || !clipboard || clipboard.length === 0}
          >
            {t('coreshop_product_quantity_price_rules_paste_ranges', { defaultValue: 'Paste' })}
            {clipboard && clipboard.length > 0 && ` (${clipboard.length})`}
          </Button>
        </Space>
      </div>

      <Table
        columns={columns}
        dataSource={rangesWithKeys}
        pagination={false}
        size="small"
        rowKey="key"
        locale={{
          emptyText: t('coreshop_product_quantity_price_rules_no_ranges', {
            defaultValue: 'No ranges defined. Click "Add" to create one.'
          })
        }}
      />
    </div>
  )
}
