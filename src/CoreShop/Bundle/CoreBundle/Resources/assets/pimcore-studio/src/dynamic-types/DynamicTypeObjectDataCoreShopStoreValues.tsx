/**
 * CoreShop CoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Tabs, InputNumber, Form, Space, Button, Typography, Card, Tooltip, Spin, Table, Divider } from 'antd'
import { UndoOutlined } from '@ant-design/icons'
import {
  DynamicTypeObjectDataAbstract
} from '@pimcore/studio-ui-bundle/modules/element'
import { useGlobalDataObjectContext } from '@pimcore/studio-ui-bundle/modules/data-object'
import { storeApi, type StoreDetail } from '@coreshop/store/src/modules/stores/api'
import { coreshopBroker, CORESHOP_EVENTS } from '@coreshop/pimcore/src/modules/broker'
import { useCurrencyConfig } from '@coreshop/currency/src/modules/currency-config'

const { Text } = Typography

// Type for unit definition info from ProductUnitDefinitions
interface UnitDefinitionInfo {
  id?: number
  unitId: number
  unitName: string
  conversionRate: number
  precision: number
  isDefault: boolean
}

// Type for unit definition price
interface ProductUnitDefinitionPrice {
  id?: number
  price?: number
  unitDefinition?: number | { id?: number }
}

interface StoreValue {
  id?: number
  price?: number
  inherited?: boolean
  inheritable?: boolean
  name?: string
  currencySymbol?: string
  values?: {
    id?: number
    price?: number
    productUnitDefinitionPrices?: ProductUnitDefinitionPrice[]
  }
}

interface StoreValuesData {
  [storeId: string]: StoreValue
}

interface StoreValuesInnerProps {
  value?: StoreValuesData
  onChange?: (value: StoreValuesData) => void
  disabled?: boolean
  style?: React.CSSProperties
}

// Module-level cache for stores
let cachedStores: StoreDetail[] | null = null
let loadPromise: Promise<StoreDetail[]> | null = null

const loadStores = async (): Promise<StoreDetail[]> => {
  if (cachedStores) return cachedStores
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const stores = await storeApi.list()
      cachedStores = stores
      return stores
    } catch (err) {
      console.error('Failed to load stores:', err)
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

const StoreValuesInner: React.FC<StoreValuesInnerProps> = ({
  value,
  onChange,
  disabled,
  style
}) => {
  // Get objectId from Pimcore Studio context
  const { context } = useGlobalDataObjectContext()
  const objectId = context?.config?.id

  // Get currency config for InputNumber precision
  const { decimalPrecision } = useCurrencyConfig()

  // Ensure value is always an object, even if null/undefined is passed
  const safeValue = value ?? {}
  const [stores, setStores] = React.useState<StoreDetail[]>([])
  const [loading, setLoading] = React.useState(true)
  const [availableUnitDefinitions, setAvailableUnitDefinitions] = React.useState<UnitDefinitionInfo[]>([])

  React.useEffect(() => {
    void (async () => {
      try {
        const loadedStores = await loadStores()
        setStores(loadedStores)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  // Subscribe to unit definition changes from ProductUnitDefinitions
  React.useEffect(() => {
    const onUnitDefinitionsChange = (data: { objectId: number; availableUnitDefinitions: UnitDefinitionInfo[] }) => {
      // Only react if same object
      if (data.objectId === objectId) {
        setAvailableUnitDefinitions(data.availableUnitDefinitions)
      }
    }

    coreshopBroker.addListener(CORESHOP_EVENTS.UNIT_DEFINITIONS_CHANGE, onUnitDefinitionsChange)

    return () => {
      coreshopBroker.removeListener(CORESHOP_EVENTS.UNIT_DEFINITIONS_CHANGE, onUnitDefinitionsChange)
    }
  }, [objectId])

  // Helper to get unit definition ID from price entry
  const getUnitDefinitionId = (unitDef?: number | { id?: number }): number | undefined => {
    if (unitDef === undefined || unitDef === null) return undefined
    if (typeof unitDef === 'number') return unitDef
    return unitDef.id
  }

  // Get unit price for a specific unit definition (backend returns display values)
  const getUnitPrice = (storeId: number, unitDefinitionId: number): number | undefined => {
    const storeValue = safeValue[storeId]
    const prices = storeValue?.values?.productUnitDefinitionPrices || []
    const priceEntry = prices.find(p => getUnitDefinitionId(p.unitDefinition) === unitDefinitionId)
    if (priceEntry?.price === undefined || priceEntry?.price === null) return undefined
    return priceEntry.price
  }

  // Handle unit price change (send display value - backend handles conversion)
  const handleUnitPriceChange = (storeId: number, unitDefinitionId: number, price: number | null) => {
    const currentStoreValue = safeValue[storeId] || {}
    const currentPrices = currentStoreValue.values?.productUnitDefinitionPrices || []

    // Find existing price entry or create new one
    const existingIndex = currentPrices.findIndex(p => getUnitDefinitionId(p.unitDefinition) === unitDefinitionId)
    let newPrices: ProductUnitDefinitionPrice[]

    if (existingIndex >= 0) {
      // Update existing
      newPrices = [...currentPrices]
      newPrices[existingIndex] = {
        ...newPrices[existingIndex],
        price: price ?? undefined
      }
    } else {
      // Add new
      newPrices = [
        ...currentPrices,
        {
          price: price ?? undefined,
          unitDefinition: unitDefinitionId
        }
      ]
    }

    onChange?.({
      ...safeValue,
      [storeId]: {
        ...currentStoreValue,
        inherited: false,
        values: {
          ...currentStoreValue.values,
          productUnitDefinitionPrices: newPrices
        }
      }
    })
  }

  // Handle main price change (send display value - backend handles conversion)
  const handlePriceChange = (storeId: number, price: number | null) => {
    const currentValue = safeValue[storeId] || {}
    onChange?.({
      ...value,
      [storeId]: {
        ...currentValue,
        price: price ?? undefined,
        inherited: false,
        values: {
          ...currentValue.values,
          price: price ?? undefined
        }
      }
    })
  }

  const handleRestoreInheritance = (storeId: number) => {
    const currentValue = safeValue[storeId]
    if (!currentValue) return

    onChange?.({
      ...safeValue,
      [storeId]: {
        ...currentValue,
        inherited: true,
        price: undefined,
        values: undefined
      }
    })
  }

  if (loading) {
    return (
      <div style={{ padding: 20, textAlign: 'center' }}>
        <Spin tip="Loading stores..." />
      </div>
    )
  }

  if (stores.length === 0) {
    return (
      <Card style={style}>
        <Text type="secondary">No stores configured</Text>
      </Card>
    )
  }

  const items = stores.map(store => {
    const storeValue = safeValue[store.id!] || {}
    const isInherited = storeValue.inherited === true
    const canRestoreInheritance = !isInherited && storeValue.inheritable

    return {
      key: String(store.id),
      label: (
        <Space>
          <span className="coreshop-icon coreshop-icon-store" style={{ marginRight: 4 }} />
          {store.name}
          {isInherited && <Text type="secondary">(inherited)</Text>}
        </Space>
      ),
      children: (
        <div style={{ padding: 16 }}>
          {canRestoreInheritance && (
            <div style={{ marginBottom: 16 }}>
              <Tooltip title="Restore inheritance from parent object">
                <Button
                  icon={<UndoOutlined />}
                  onClick={() => handleRestoreInheritance(store.id!)}
                  disabled={disabled}
                  size="small"
                >
                  Restore Inheritance
                </Button>
              </Tooltip>
            </div>
          )}

          <Form layout="vertical">
            <Form.Item label="Price (net)">
              <InputNumber
                value={storeValue.price ?? storeValue.values?.price}
                onChange={(val) => handlePriceChange(store.id!, val)}
                disabled={disabled || isInherited}
                precision={decimalPrecision}
                style={{ width: '100%' }}
                addonAfter={storeValue.currencySymbol || '€'}
              />
            </Form.Item>
          </Form>

          {/* Unit Prices Table - only show when additional (non-default) unit definitions exist */}
          {availableUnitDefinitions.filter(u => !u.isDefault).length > 0 && (
            <>
              <Divider />
              <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>Unit Prices</Text>
                <Table
                  dataSource={availableUnitDefinitions.filter(u => !u.isDefault)}
                  columns={[
                    {
                      title: 'Unit',
                      dataIndex: 'unitName',
                      key: 'unit',
                      width: 150
                    },
                    {
                      title: 'Conversion Rate',
                      dataIndex: 'conversionRate',
                      key: 'conversionRate',
                      width: 120
                    },
                    {
                      title: 'Price',
                      key: 'price',
                      render: (_: any, unitDef: UnitDefinitionInfo) => {
                        // Only show price field for units with id (saved)
                        if (!unitDef.id) {
                          return <Text type="secondary">Save unit first</Text>
                        }
                        return (
                          <InputNumber
                            value={getUnitPrice(store.id!, unitDef.id)}
                            onChange={(v) => handleUnitPriceChange(store.id!, unitDef.id!, v)}
                            disabled={disabled || isInherited}
                            precision={decimalPrecision}
                            style={{ width: 150 }}
                            addonAfter={storeValue.currencySymbol || '€'}
                          />
                        )
                      }
                    }
                  ]}
                  rowKey="unitId"
                  pagination={false}
                  size="small"
                />
              </div>
            </>
          )}

          {isInherited && (
            <Text type="secondary" italic>
              Value is inherited from parent object
            </Text>
          )}
        </div>
      )
    }
  })

  return (
    <Card style={style} styles={{ body: { padding: 0 } }}>
      <Tabs
        items={items}
        defaultActiveKey={stores[0]?.id?.toString()}
        tabPosition="top"
        style={{ minHeight: 200 }}
      />
    </Card>
  )
}

export class DynamicTypeObjectDataCoreShopStoreValues extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopStoreValues'

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <StoreValuesInner
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
