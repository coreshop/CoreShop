/**
 * CoreShop ProductBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Card, Form, Select, InputNumber, Button, Space, Table, Typography, Spin, Empty, Collapse, Tooltip } from 'antd'
import { PlusOutlined, DeleteOutlined, LockOutlined } from '@ant-design/icons'
import {
  DynamicTypeObjectDataAbstract
} from '@pimcore/studio-ui-bundle/modules/element'
import { useGlobalDataObjectContext } from '@pimcore/studio-ui-bundle/modules/data-object'
import { productUnitApi, type ProductUnitDetail } from '../modules/product-units/api'
import { coreshopBroker, CORESHOP_EVENTS } from '@coreshop/pimcore/src/modules/broker'

const { Text } = Typography

interface UnitObject {
  id: number
  name: string
  fullLabel?: string
}

interface UnitDefinition {
  id?: number
  unit?: UnitObject | number
  conversionRate?: number
  precision?: number
  _tempKey?: string // Temporary key for unsaved entries
}

interface UnitDefinitionsValue {
  id?: number
  defaultUnitDefinition?: UnitDefinition
  unitDefinitions?: UnitDefinition[]
}

// Helper to get unit ID from unit field (can be object or number)
const getUnitId = (unit?: UnitObject | number): number | undefined => {
  if (unit === undefined || unit === null) return undefined
  if (typeof unit === 'number') return unit
  return unit.id
}

interface UnitDefinitionsInnerProps {
  value?: UnitDefinitionsValue
  onChange?: (value: UnitDefinitionsValue) => void
  disabled?: boolean
  style?: React.CSSProperties
}

// Type for the event payload
export interface UnitDefinitionInfo {
  id?: number
  unitId: number
  unitName: string
  conversionRate: number
  precision: number
  isDefault: boolean
}

// Module-level cache for product units
let cachedUnits: ProductUnitDetail[] | null = null
let loadPromise: Promise<ProductUnitDetail[]> | null = null

const loadUnits = async (): Promise<ProductUnitDetail[]> => {
  if (cachedUnits) return cachedUnits
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const units = await productUnitApi.list()
      cachedUnits = units
      return units
    } catch (err) {
      console.error('Failed to load product units:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

const UnitDefinitionsInner: React.FC<UnitDefinitionsInnerProps> = ({
  value,
  onChange,
  disabled,
  style
}) => {
  // Get objectId from Pimcore Studio context
  const { context } = useGlobalDataObjectContext()
  const objectId = context?.config?.id

  // Ensure value is always an object, even if null/undefined is passed
  const safeValue: UnitDefinitionsValue = value ?? {}
  const [units, setUnits] = React.useState<ProductUnitDetail[]>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      try {
        const loadedUnits = await loadUnits()
        setUnits(loadedUnits)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const unitOptions = React.useMemo(() =>
    units.map(u => ({
      value: u.id!,
      label: u.name
    })),
    [units]
  )

  // Filter out the default unit from additional units for display
  // Compare by unit ID, not by definition ID (handles new unsaved entries)
  const additionalUnits = React.useMemo(() => {
    const defaultUnitId = getUnitId(safeValue.defaultUnitDefinition?.unit)
    return (safeValue.unitDefinitions || []).filter(def => {
      const defUnitId = getUnitId(def.unit)
      // Keep entries that have a different unit than the default
      // Also keep entries with no unit selected yet (new empty entries)
      return defUnitId !== defaultUnitId || defUnitId === undefined
    })
  }, [safeValue.unitDefinitions, safeValue.defaultUnitDefinition])

  // Get all currently used unit IDs (default + additional units, but not duplicates)
  const usedUnitIds = React.useMemo(() => {
    const ids: Set<number> = new Set()
    const defaultUnitId = getUnitId(safeValue.defaultUnitDefinition?.unit)
    if (defaultUnitId !== undefined) {
      ids.add(defaultUnitId)
    }
    // Only count additional units (those not matching the default unit definition)
    for (const def of additionalUnits) {
      const unitId = getUnitId(def.unit)
      if (unitId !== undefined) {
        ids.add(unitId)
      }
    }
    return Array.from(ids)
  }, [safeValue.defaultUnitDefinition, additionalUnits])

  // Fire event when unit definitions change (for StoreValues to listen)
  const dispatchUnitDefinitionChangeEvent = React.useCallback(() => {
    if (!objectId || loading) return

    const availableUnitDefinitions: UnitDefinitionInfo[] = []

    // Default Unit
    if (safeValue.defaultUnitDefinition?.unit) {
      const unitId = getUnitId(safeValue.defaultUnitDefinition.unit)
      const unit = units.find(u => u.id === unitId)
      if (unit) {
        availableUnitDefinitions.push({
          id: safeValue.defaultUnitDefinition.id,
          unitId: unit.id!,
          unitName: unit.name ?? '',
          conversionRate: 1,
          precision: safeValue.defaultUnitDefinition.precision ?? 0,
          isDefault: true
        })
      }
    }

    // Additional Units (already filtered)
    for (const def of additionalUnits) {
      const unitId = getUnitId(def.unit)
      if (unitId === undefined) continue

      const unit = units.find(u => u.id === unitId)
      if (unit) {
        availableUnitDefinitions.push({
          id: def.id,
          unitId: unit.id!,
          unitName: unit.name ?? '',
          conversionRate: def.conversionRate ?? 1,
          precision: def.precision ?? 0,
          isDefault: false
        })
      }
    }

    // Fire event like ExtJS: coreshop.broker.fireEvent(...)
    coreshopBroker.fireEvent(CORESHOP_EVENTS.UNIT_DEFINITIONS_CHANGE, {
      objectId,
      availableUnitDefinitions
    })
  }, [objectId, loading, safeValue.defaultUnitDefinition, additionalUnits, units])

  // Dispatch event on value change and initial load
  React.useEffect(() => {
    dispatchUnitDefinitionChangeEvent()
  }, [dispatchUnitDefinitionChangeEvent])

  // Get available options for a specific record (exclude used units except the record's own unit)
  const getAvailableOptions = (currentUnitId?: number) => {
    return unitOptions.filter(opt =>
      opt.value === currentUnitId || !usedUnitIds.includes(opt.value)
    )
  }

  const handleDefaultUnitChange = (field: string, newValue: any) => {
    onChange?.({
      ...safeValue,
      defaultUnitDefinition: {
        precision: 0, // Default value
        ...safeValue.defaultUnitDefinition,
        [field]: newValue
      }
    })
  }

  const handleAdditionalUnitChange = (record: UnitDefinition, field: string, newValue: any) => {
    const newDefs = (safeValue.unitDefinitions || []).map(def => {
      // For saved records, compare by ID
      if (record.id !== undefined && def.id === record.id) {
        return { ...def, [field]: newValue }
      }
      // For unsaved records, compare by _tempKey
      if (record.id === undefined && record._tempKey && def._tempKey === record._tempKey) {
        return { ...def, [field]: newValue }
      }
      return def
    })
    onChange?.({
      ...safeValue,
      unitDefinitions: newDefs
    })
  }

  const addAdditionalUnit = () => {
    const newDefs = [...(safeValue.unitDefinitions || []), {
      unit: undefined,
      conversionRate: 1,
      precision: 0,
      _tempKey: `new-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
    }]
    onChange?.({
      ...safeValue,
      unitDefinitions: newDefs
    })
  }

  const removeAdditionalUnit = (record: UnitDefinition) => {
    const newDefs = (safeValue.unitDefinitions || []).filter(def => {
      // For saved records, compare by ID
      if (record.id !== undefined) {
        return def.id !== record.id
      }
      // For unsaved records, compare by _tempKey
      if (record._tempKey) {
        return def._tempKey !== record._tempKey
      }
      return true
    })
    onChange?.({
      ...safeValue,
      unitDefinitions: newDefs
    })
  }

  if (loading) {
    return (
      <div style={{ padding: 20, textAlign: 'center' }}>
        <Spin tip="Loading units..." />
      </div>
    )
  }

  if (units.length === 0) {
    return (
      <Card style={style}>
        <Empty
          description={
            <Text type="secondary">
              No product units available. Please configure product units first.
            </Text>
          }
        />
      </Card>
    )
  }

  const additionalColumns = [
    {
      title: 'Unit',
      dataIndex: 'unit',
      key: 'unit',
      render: (_: any, record: UnitDefinition) => {
        // Once saved (has id), unit cannot be changed
        const isReadOnly = record.id !== undefined && record.id !== null
        const currentUnitId = getUnitId(record.unit)
        // For read-only records, show all options; for editable, filter available ones
        const options = isReadOnly ? unitOptions : getAvailableOptions(currentUnitId)
        const select = (
          <Select
            value={currentUnitId}
            onChange={(v) => handleAdditionalUnitChange(record, 'unit', v)}
            options={options}
            style={{ width: 150 }}
            disabled={disabled || isReadOnly}
            placeholder="Select unit"
            suffixIcon={isReadOnly ? <LockOutlined /> : undefined}
          />
        )
        if (isReadOnly) {
          return (
            <Tooltip title="Unit cannot be changed after saving">
              {select}
            </Tooltip>
          )
        }
        return select
      }
    },
    {
      title: 'Conversion Rate',
      dataIndex: 'conversionRate',
      key: 'conversionRate',
      render: (_: any, record: UnitDefinition) => (
        <InputNumber
          value={record.conversionRate}
          onChange={(v) => handleAdditionalUnitChange(record, 'conversionRate', v)}
          min={0.0001}
          step={0.01}
          precision={4}
          disabled={disabled}
          style={{ width: 120 }}
        />
      )
    },
    {
      title: 'Precision',
      dataIndex: 'precision',
      key: 'precision',
      render: (_: any, record: UnitDefinition) => (
        <InputNumber
          value={record.precision}
          onChange={(v) => handleAdditionalUnitChange(record, 'precision', v)}
          min={0}
          max={10}
          disabled={disabled}
          style={{ width: 80 }}
        />
      )
    },
    {
      title: '',
      key: 'actions',
      width: 50,
      render: (_: any, record: UnitDefinition) => (
        <Button
          type="text"
          danger
          icon={<DeleteOutlined />}
          onClick={() => removeAdditionalUnit(record)}
          disabled={disabled}
        />
      )
    }
  ]

  const collapseItems = [
    {
      key: 'unit-definitions',
      label: 'Unit Definitions',
      children: (
        <div style={{ padding: 16 }}>
          <Form layout="vertical">
            <Form.Item label="Default Unit">
              <Space>
                {(() => {
                  // Once saved (has id), unit cannot be changed
                  const isReadOnly = safeValue.defaultUnitDefinition?.id !== undefined && safeValue.defaultUnitDefinition?.id !== null
                  const select = (
                    <Select
                      value={getUnitId(safeValue.defaultUnitDefinition?.unit)}
                      onChange={(v) => handleDefaultUnitChange('unit', v)}
                      options={unitOptions}
                      style={{ width: 200 }}
                      disabled={disabled || isReadOnly}
                      placeholder="Select default unit"
                      suffixIcon={isReadOnly ? <LockOutlined /> : undefined}
                    />
                  )
                  if (isReadOnly) {
                    return (
                      <Tooltip title="Unit cannot be changed after saving">
                        {select}
                      </Tooltip>
                    )
                  }
                  return select
                })()}
                <Text type="secondary">Precision:</Text>
                <InputNumber
                  value={safeValue.defaultUnitDefinition?.precision ?? 0}
                  onChange={(v) => handleDefaultUnitChange('precision', v)}
                  min={0}
                  max={10}
                  disabled={disabled}
                  style={{ width: 80 }}
                />
              </Space>
            </Form.Item>
          </Form>

          <div style={{ marginTop: 16 }}>
            <Space style={{ marginBottom: 8 }}>
              <Text strong>Additional Units</Text>
              {!disabled && (() => {
                const defaultUnitId = getUnitId(safeValue.defaultUnitDefinition?.unit)
                const hasDefaultUnit = defaultUnitId !== undefined
                const hasAvailableOptions = getAvailableOptions(undefined).length > 0

                if (!hasDefaultUnit) {
                  return (
                    <Tooltip title="Please select a default unit first">
                      <Button
                        type="primary"
                        size="small"
                        icon={<PlusOutlined />}
                        disabled
                      >
                        Add Unit
                      </Button>
                    </Tooltip>
                  )
                }

                if (!hasAvailableOptions) return null

                return (
                  <Button
                    type="primary"
                    size="small"
                    icon={<PlusOutlined />}
                    onClick={addAdditionalUnit}
                  >
                    Add Unit
                  </Button>
                )
              })()}
            </Space>

            {additionalUnits.length > 0 ? (
              <Table
                dataSource={additionalUnits}
                columns={additionalColumns}
                rowKey={(record) => record.id?.toString() ?? record._tempKey ?? 'unknown'}
                pagination={false}
                size="small"
              />
            ) : (
              <Text type="secondary" italic>No additional units defined</Text>
            )}
          </div>
        </div>
      )
    }
  ]

  return (
    <Card style={style} styles={{ body: { padding: 0 } }}>
      <Collapse
        items={collapseItems}
        defaultActiveKey={['unit-definitions']}
        bordered={false}
      />
    </Card>
  )
}

export class DynamicTypeObjectDataCoreShopProductUnitDefinitions extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopProductUnitDefinitions'

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <UnitDefinitionsInner
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
      />
    )
  }

  getVersionObjectDataComponent(props: any): React.ReactElement {
    return this.getObjectDataComponent({ ...props, noteditable: true })
  }
}
