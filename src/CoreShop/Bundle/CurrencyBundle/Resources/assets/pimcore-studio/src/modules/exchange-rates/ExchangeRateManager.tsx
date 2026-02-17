/**
 * CoreShop CurrencyBundle Studio Plugin
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
import { Button, Table, InputNumber, Select, Popconfirm, Space } from 'antd'
import { PlusOutlined, DeleteOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import { exchangeRateApi, type ExchangeRate } from './api'
import { currencyApi } from '../currencies/api'

interface ExchangeRateRow extends ExchangeRate {
  isEditing?: boolean
  isNew?: boolean
}

export const ExchangeRateManager: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [exchangeRates, setExchangeRates] = React.useState<ExchangeRateRow[]>([])
  const [currencies, setCurrencies] = React.useState<Array<{ id: number, name: string }>>([])
  const [loading, setLoading] = React.useState(false)
  const [editingKey, setEditingKey] = React.useState<string | null>(null)
  const [editingData, setEditingData] = React.useState<Partial<ExchangeRate>>({})

  // Load currencies
  React.useEffect(() => {
    currencyApi.list()
      .then((rows: any) => {
        const list = Array.isArray(rows) ? rows : []
        setCurrencies(list.map((r: any) => ({ id: r.id, name: r.name })))
      })
      .catch(() => setCurrencies([]))
  }, [])

  // Load exchange rates
  const loadExchangeRates = React.useCallback(() => {
    setLoading(true)
    exchangeRateApi.list()
      .then((rows: any) => {
        const list = Array.isArray(rows) ? rows : []
        setExchangeRates(list)
      })
      .catch(() => {
        void messageApi.error(renderApiError(t('coreshop_error_loading', { defaultValue: 'Failed to load exchange rates' })))
        setExchangeRates([])
      })
      .finally(() => setLoading(false))
  }, [])

  React.useEffect(() => {
    loadExchangeRates()
  }, [loadExchangeRates])

  // Generate temp key for new rows
  const getTempKey = () => `temp_${Date.now()}_${Math.random()}`

  // Add new exchange rate (inline, not saved yet)
  const handleAdd = () => {
    const tempKey = getTempKey()
    const newRow: ExchangeRateRow = {
      fromCurrency: undefined as any,
      toCurrency: undefined as any,
      exchangeRate: 1,
      isEditing: true,
      isNew: true
    }

    setExchangeRates([newRow, ...exchangeRates])
    setEditingKey(tempKey)
    setEditingData(newRow)
  }

  // Start editing existing row
  const handleEdit = (record: ExchangeRateRow, index: number) => {
    const key = record.id?.toString() || `temp_${index}`
    setEditingKey(key)
    setEditingData({ ...record })
  }

  // Cancel editing
  const handleCancel = () => {
    // Remove new unsaved rows
    setExchangeRates(prev => prev.filter(r => !r.isNew))
    setEditingKey(null)
    setEditingData({})
  }

  // Save changes
  const handleSave = async () => {
    if (!editingData.fromCurrency || !editingData.toCurrency || !editingData.exchangeRate) {
      void messageApi.error(renderApiError(t('coreshop_fill_all_fields', { defaultValue: 'Please fill all fields' })))
      return
    }

    try {
      // Always use save endpoint (no separate add endpoint)
      await exchangeRateApi.save(editingData as ExchangeRate)
      void messageApi.success(editingData.id ? t('coreshop_exchange_rate_updated', { defaultValue: 'Exchange rate updated' }) : t('coreshop_exchange_rate_created', { defaultValue: 'Exchange rate created' }))

      // Reload data
      setEditingKey(null)
      setEditingData({})
      loadExchangeRates()
    } catch (error) {
      void messageApi.error(renderApiError(t('coreshop_error_saving', { defaultValue: 'Failed to save exchange rate' })))
    }
  }

  // Update editing data
  const handleFieldChange = (field: keyof ExchangeRate, value: any) => {
    setEditingData(prev => ({ ...prev, [field]: value }))
  }

  // Delete exchange rate
  const handleDelete = async (id: number) => {
    try {
      await exchangeRateApi.delete(id)
      void messageApi.success(t('coreshop_exchange_rate_deleted', { defaultValue: 'Exchange rate deleted' }))
      loadExchangeRates()
    } catch (error) {
      void messageApi.error(renderApiError(t('coreshop_error_deleting', { defaultValue: 'Failed to delete exchange rate' })))
    }
  }

  const isEditing = (record: ExchangeRateRow, index: number) => {
    const key = record.id?.toString() || `temp_${index}`
    return editingKey === key || record.isNew
  }

  const columns = [
    {
      title: t('coreshop_from_currency', { defaultValue: 'From Currency' }),
      dataIndex: 'fromCurrency',
      key: 'fromCurrency',
      width: '35%',
      render: (value: number, record: ExchangeRateRow, index: number) => {
        if (isEditing(record, index)) {
          return (
            <Select
              value={editingData.fromCurrency}
              onChange={(newValue) => handleFieldChange('fromCurrency', newValue)}
              options={currencies.map(c => ({ value: c.id, label: c.name }))}
              style={{ width: '100%' }}
              showSearch
              optionFilterProp="label"
            />
          )
        }
        const currency = currencies.find(c => c.id === value)
        return currency?.name || value
      }
    },
    {
      title: t('coreshop_to_currency', { defaultValue: 'To Currency' }),
      dataIndex: 'toCurrency',
      key: 'toCurrency',
      width: '35%',
      render: (value: number, record: ExchangeRateRow, index: number) => {
        if (isEditing(record, index)) {
          return (
            <Select
              value={editingData.toCurrency}
              onChange={(newValue) => handleFieldChange('toCurrency', newValue)}
              options={currencies.map(c => ({ value: c.id, label: c.name }))}
              style={{ width: '100%' }}
              showSearch
              optionFilterProp="label"
            />
          )
        }
        const currency = currencies.find(c => c.id === value)
        return currency?.name || value
      }
    },
    {
      title: t('coreshop_exchange_rate', { defaultValue: 'Exchange Rate' }),
      dataIndex: 'exchangeRate',
      key: 'exchangeRate',
      width: '20%',
      render: (value: number, record: ExchangeRateRow, index: number) => {
        if (isEditing(record, index)) {
          return (
            <InputNumber
              value={editingData.exchangeRate}
              onChange={(newValue) => handleFieldChange('exchangeRate', newValue ?? 0)}
              precision={10}
              min={0}
              step={0.0001}
              style={{ width: '100%' }}
            />
          )
        }
        return value
      }
    },
    {
      title: '',
      key: 'actions',
      width: '10%',
      render: (_: any, record: ExchangeRateRow, index: number) => {
        if (isEditing(record, index)) {
          return (
            <Space>
              <Button
                type="text"
                icon={<CheckOutlined />}
                onClick={handleSave}
                style={{ color: '#52c41a' }}
              />
              <Button
                type="text"
                icon={<CloseOutlined />}
                onClick={handleCancel}
                danger
              />
            </Space>
          )
        }

        return (
          <Space>
            <Button
              type="text"
              onClick={() => handleEdit(record, index)}
            >
              {t('coreshop_edit', { defaultValue: 'Edit' })}
            </Button>
            {record.id && (
              <Popconfirm
                title={t('coreshop_delete_exchange_rate_confirm', { defaultValue: 'Delete exchange rate?' })}
                onConfirm={() => handleDelete(record.id!)}
                okText={t('yes', { defaultValue: 'Yes' })}
                cancelText={t('no', { defaultValue: 'No' })}
              >
                <Button type="text" icon={<DeleteOutlined />} danger />
              </Popconfirm>
            )}
          </Space>
        )
      }
    }
  ]

  return (
    <div style={{ padding: 24 }}>
      <Space direction="vertical" size="large" style={{ width: '100%' }}>
        <div>
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={handleAdd}
            disabled={editingKey !== null}
          >
            {t('coreshop_add', { defaultValue: 'Add' })}
          </Button>
        </div>

        <Table
          columns={columns}
          dataSource={exchangeRates}
          loading={loading}
          rowKey={(record, index) => record.id?.toString() || `temp_${index}`}
          pagination={false}
          bordered
          size="middle"
        />
      </Space>
    </div>
  )
}
