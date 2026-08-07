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
import { Table, Button, Space, InputNumber, Select, Switch } from 'antd'
import { PlusOutlined, DeleteOutlined, ArrowUpOutlined, ArrowDownOutlined, CopyOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { QuantityRange, PricingBehaviour } from '../types'

interface RangesPanelProps {
  ranges: QuantityRange[]
  onChange: (ranges: QuantityRange[]) => void
  pricingBehaviourTypes?: Array<[string, string]>
  disabled?: boolean
  objectId?: number | string
}

/**
 * Behaviour type categories for conditional field editability
 * Reference: CoreBundle/Resources/public/pimcore/js/productquantitypricerules/ranges.js
 */
const AMOUNT_BASED_BEHAVIOURS: PricingBehaviour[] = ['fixed', 'amount_decrease', 'amount_increase']
const PERCENT_BASED_BEHAVIOURS: PricingBehaviour[] = ['percentage_decrease', 'percentage_increase']

const isAmountBased = (behaviour: PricingBehaviour | undefined): boolean =>
  AMOUNT_BASED_BEHAVIOURS.includes(behaviour ?? 'fixed')

const isPercentBased = (behaviour: PricingBehaviour | undefined): boolean =>
  PERCENT_BASED_BEHAVIOURS.includes(behaviour ?? 'fixed')

/**
 * Module-level cache for currency options
 * Avoids multiple API calls when multiple selects are rendered
 */
let cachedCurrencyOptions: Array<{ value: number, label: string }> | null = null
let currencyLoadPromise: Promise<Array<{ value: number, label: string }>> | null = null

const loadCurrencyOptions = async (): Promise<Array<{ value: number, label: string }>> => {
  if (cachedCurrencyOptions) return cachedCurrencyOptions
  if (currencyLoadPromise) return currencyLoadPromise

  currencyLoadPromise = (async () => {
    try {
      const response = await fetch('/pimcore-studio/api/coreshop/currencies/list', {
        credentials: 'same-origin'
      })
      if (!response.ok) throw new Error('Failed to load currencies')
      const data = await response.json()
      // API returns array directly (EntityListResponse extends Array<T>)
      const items = Array.isArray(data) ? data : []
      cachedCurrencyOptions = items.map((c: { id: number, name?: string, isoCode?: string }) => ({
        value: c.id,
        label: c.name ?? c.isoCode ?? `#${c.id}`
      }))
      return cachedCurrencyOptions
    } catch (err) {
      console.error('Failed to load currencies:', err)
      return []
    } finally {
      currencyLoadPromise = null
    }
  })()
  return currencyLoadPromise
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
 * RangesPanel - Editable grid for quantity price ranges
 * Matches ExtJS layout with all columns
 */
export const RangesPanel: React.FC<RangesPanelProps> = ({
  ranges,
  onChange,
  pricingBehaviourTypes = DEFAULT_PRICING_BEHAVIOURS,
  disabled = false,
  objectId
}) => {
  const { t } = useTranslation()
  const [clipboard, setClipboard] = React.useState<QuantityRange[] | null>(null)
  const [currencyOptions, setCurrencyOptions] = React.useState<Array<{ value: number, label: string }>>(cachedCurrencyOptions || [])
  const [currencyLoading, setCurrencyLoading] = React.useState(!cachedCurrencyOptions)
  const [unitOptions, setUnitOptions] = React.useState<Array<{ value: number, label: string }>>([])

  // Load currency options on mount
  React.useEffect(() => {
    (async () => {
      if (!cachedCurrencyOptions) {
        setCurrencyLoading(true)
      }
      try {
        const opts = await loadCurrencyOptions()
        setCurrencyOptions(opts)
      } finally {
        setCurrencyLoading(false)
      }
    })()
  }, [])

  // Load unit definitions via API when objectId is available
  React.useEffect(() => {
    if (!objectId) {
      setUnitOptions([])
      return
    }

    const loadUnitDefinitions = async () => {
      try {
        const response = await fetch(
          `/pimcore-studio/api/coreshop/product_unit_definitions/get-product-unit-definitions?productId=${objectId}`,
          { credentials: 'same-origin' }
        )
        if (!response.ok) {
          setUnitOptions([])
          return
        }
        const data = await response.json()
        const opts = (Array.isArray(data) ? data : []).map((ud: { id: number, unit?: { name?: string, fullLabel?: string } }) => ({
          value: ud.id,
          label: ud.unit?.fullLabel ?? ud.unit?.name ?? `#${ud.id}`
        }))
        setUnitOptions(opts)
      } catch (err) {
        console.error('Failed to load unit definitions:', err)
        setUnitOptions([])
      }
    }

    loadUnitDefinitions()
  }, [objectId])

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
    label: t(labelKey, { defaultValue: labelKey.replace(/coreshop_product_quantity_price_rules_behaviour_/g, '').replace(/_/g, ' ') })
  }))

  // Handle adding a new range
  // Reference: CoreBundle/Resources/public/pimcore/js/productquantitypricerules/ranges.js parseNewModelClass()
  const handleAdd = () => {
    const lastRange = ranges[ranges.length - 1]

    // Determine unit: use last range's unit, or if only one unit exists, use that
    let unitDefinition: number | null = null
    if (lastRange?.unitDefinition) {
      unitDefinition = lastRange.unitDefinition
    } else if (unitOptions.length === 1) {
      unitDefinition = unitOptions[0].value
    }

    const newRange: QuantityRange = {
      id: null,
      rangeStartingFrom: lastRange ? lastRange.rangeStartingFrom + 10 : 0,
      pricingBehaviour: 'fixed',
      unitDefinition,
      amount: 0,
      percentage: 0,
      currency: lastRange?.currency ?? null,
      pseudoPrice: 0,
      highlighted: false
    }
    onChange([...ranges, newRange])
  }

  // Handle updating a range
  const handleUpdate = (index: number, field: keyof QuantityRange, value: any) => {
    const newRanges = [...ranges]
    let updatedRange = { ...newRanges[index], [field]: value }

    // Reset related fields when pricingBehaviour changes
    // Reference: CoreBundle/Resources/public/pimcore/js/productquantitypricerules/ranges.js onPriceBehaviourChange()
    if (field === 'pricingBehaviour') {
      if (isPercentBased(value as PricingBehaviour)) {
        updatedRange.amount = 0
        updatedRange.pseudoPrice = 0
        updatedRange.currency = null
      } else if (isAmountBased(value as PricingBehaviour)) {
        updatedRange.percentage = 0
      }
    }

    newRanges[index] = updatedRange
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

  // Table columns matching ExtJS layout
  const columns = [
    {
      title: t('coreshop_product_quantity_price_rules_range_starting_from', { defaultValue: 'Starting from' }),
      dataIndex: 'rangeStartingFrom',
      key: 'rangeStartingFrom',
      width: 120,
      render: (value: number, _record: QuantityRange, index: number) => (
        disabled ? (
          <span>{value} {t('coreshop_product_quantity_price_rules_units', { defaultValue: 'Units' })}</span>
        ) : (
          <InputNumber
            value={value}
            min={0}
            precision={0}
            size="small"
            style={{ width: 80 }}
            onChange={(val) => handleUpdate(index, 'rangeStartingFrom', val ?? 0)}
          />
        )
      )
    },
    {
      title: t('coreshop_product_quantity_price_rules_calculation_behaviour', { defaultValue: 'Calculation Behaviour' }),
      dataIndex: 'pricingBehaviour',
      key: 'pricingBehaviour',
      width: 180,
      render: (value: PricingBehaviour, _record: QuantityRange, index: number) => (
        disabled ? (
          <span>{pricingOptions.find(o => o.value === value)?.label || value}</span>
        ) : (
          <Select
            value={value}
            options={pricingOptions}
            size="small"
            style={{ width: '100%' }}
            onChange={(val) => handleUpdate(index, 'pricingBehaviour', val)}
          />
        )
      )
    },
    // Unit column - only shown if product has unit definitions (via broker event)
    ...(unitOptions.length > 0 ? [{
      title: t('coreshop_product_quantity_price_rules_unit_definition', { defaultValue: 'Unit' }),
      dataIndex: 'unitDefinition',
      key: 'unitDefinition',
      width: 140,
      render: (value: number | null | undefined, _record: QuantityRange, index: number) => (
        disabled ? (
          <span>{value ? (unitOptions.find(o => o.value === value)?.label ?? `#${value}`) : '--'}</span>
        ) : (
          <Select
            value={value}
            options={unitOptions}
            size="small"
            style={{ width: '100%' }}
            onChange={(val) => handleUpdate(index, 'unitDefinition', val ?? null)}
            placeholder={t('coreshop_select', { defaultValue: 'Select' })}
          />
        )
      )
    }] : []),
    {
      title: t('coreshop_product_quantity_price_rules_highlight', { defaultValue: 'Highlight' }),
      dataIndex: 'highlighted',
      key: 'highlighted',
      width: 80,
      render: (value: boolean, _record: QuantityRange, index: number) => (
        disabled ? (
          <span>{value ? t('yes', { defaultValue: 'Yes' }) : t('no', { defaultValue: 'No' })}</span>
        ) : (
          <Switch
            checked={value}
            size="small"
            onChange={(checked) => handleUpdate(index, 'highlighted', checked)}
          />
        )
      )
    },
    {
      title: t('coreshop_product_quantity_price_rules_amount', { defaultValue: 'Amount' }),
      dataIndex: 'amount',
      key: 'amount',
      width: 100,
      render: (value: number | undefined, record: QuantityRange, index: number) => {
        const isEditable = isAmountBased(record.pricingBehaviour)
        return disabled || !isEditable ? (
          <span style={{ color: !isEditable ? '#999' : undefined, fontStyle: !isEditable ? 'italic' : undefined }}>
            {(value ?? 0).toFixed(2)}
          </span>
        ) : (
          <InputNumber
            value={value ?? 0}
            min={0}
            precision={2}
            size="small"
            style={{ width: 80 }}
            onChange={(val) => handleUpdate(index, 'amount', val ?? 0)}
          />
        )
      }
    },
    {
      title: t('coreshop_product_quantity_price_rules_currency', { defaultValue: 'Currency' }),
      dataIndex: 'currency',
      key: 'currency',
      width: 140,
      render: (value: number | null | undefined, record: QuantityRange, index: number) => {
        const isEditable = isAmountBased(record.pricingBehaviour)
        return disabled || !isEditable ? (
          <span style={{ color: '#999', fontStyle: 'italic' }}>
            {value
              ? (currencyOptions.find(o => o.value === value)?.label ?? `#${value}`)
              : t('coreshop_empty', { defaultValue: 'Empty' })}
          </span>
        ) : (
          <Select
            value={value}
            options={currencyOptions}
            loading={currencyLoading}
            size="small"
            style={{ width: '100%' }}
            allowClear
            onChange={(val) => handleUpdate(index, 'currency', val ?? null)}
            placeholder={t('coreshop_select', { defaultValue: 'Select' })}
            showSearch
            filterOption={(input, option) =>
              (option?.label ?? '').toString().toLowerCase().includes(input.toLowerCase())
            }
          />
        )
      }
    },
    {
      title: t('coreshop_product_quantity_price_rules_percentage', { defaultValue: 'Percentage' }),
      dataIndex: 'percentage',
      key: 'percentage',
      width: 100,
      render: (value: number | undefined, record: QuantityRange, index: number) => {
        const isEditable = isPercentBased(record.pricingBehaviour)
        return disabled || !isEditable ? (
          <span style={{ color: !isEditable ? '#999' : undefined, fontStyle: !isEditable ? 'italic' : undefined }}>
            {isEditable ? `${value ?? 0}%` : '0%'}
          </span>
        ) : (
          <InputNumber
            value={value ?? 0}
            min={0}
            max={100}
            precision={2}
            size="small"
            style={{ width: 70 }}
            addonAfter="%"
            onChange={(val) => handleUpdate(index, 'percentage', val ?? 0)}
          />
        )
      }
    },
    {
      title: t('coreshop_product_quantity_price_rules_pseudo_price', { defaultValue: 'Pseudo Price' }),
      dataIndex: 'pseudoPrice',
      key: 'pseudoPrice',
      width: 100,
      render: (value: number | undefined, record: QuantityRange, index: number) => {
        const isEditable = isAmountBased(record.pricingBehaviour)
        return disabled || !isEditable ? (
          <span style={{ color: !isEditable ? '#999' : undefined, fontStyle: !isEditable ? 'italic' : undefined }}>
            {(value ?? 0).toFixed(2)}
          </span>
        ) : (
          <InputNumber
            value={value ?? 0}
            min={0}
            precision={2}
            size="small"
            style={{ width: 80 }}
            onChange={(val) => handleUpdate(index, 'pseudoPrice', val ?? 0)}
          />
        )
      }
    },
    {
      title: '',
      key: 'actions',
      width: 90,
      render: (_: any, _record: QuantityRange, index: number) => (
        <Space size={2}>
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
      {/* Toolbar */}
      <div style={{ marginBottom: 8 }}>
        <Space>
          <Button
            size="small"
            type="primary"
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
            {t('coreshop_product_quantity_price_rules_copy_ranges', { defaultValue: 'Copy Ranges' })}
          </Button>
          {clipboard && clipboard.length > 0 && (
            <Button
              size="small"
              onClick={() => {
                const newRanges = clipboard.map(r => ({ ...r, id: null }))
                onChange([...ranges, ...newRanges])
              }}
              disabled={disabled}
            >
              {t('coreshop_product_quantity_price_rules_paste_ranges', { defaultValue: 'Paste' })} ({clipboard.length})
            </Button>
          )}
        </Space>
      </div>

      {/* Table */}
      <Table
        columns={columns}
        dataSource={rangesWithKeys}
        pagination={false}
        size="small"
        rowKey="key"
        bordered
        scroll={{ x: 'max-content' }}
        locale={{
          emptyText: t('coreshop_product_quantity_price_rules_no_ranges', {
            defaultValue: 'No ranges defined. Click "Add" to create one.'
          })
        }}
      />
    </div>
  )
}
