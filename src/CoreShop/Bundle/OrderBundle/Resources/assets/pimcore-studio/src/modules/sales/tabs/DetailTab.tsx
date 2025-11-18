/**
 * CoreShop OrderBundle Detail Tab
 *
 * Displays order items, price rules, and summary totals (read-only).
 * Pattern from ExtJS: /order/detail/blocks/detail.js
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
import { Table, Card } from 'antd'
import { createStyles } from 'antd-style'
import { formatCurrency, getCurrencyCode } from '@coreshop/pimcore/src/utils'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps } from '../registry'
import { useSaleContext } from '../context/SaleActionsContext'

interface DetailItem {
  id: number
  productName: string
  priceNet: number
  price: number
  quantity: number
  unit?: string
  total: number
  totalTax: number
  convertedPriceNet?: number
  convertedPrice?: number
  convertedTotal?: number
  convertedTotalTax?: number
}

interface PriceRule {
  name: string
  code?: string
  discount: number
  convertedDiscount?: number
}

interface SummaryItem {
  key: string
  text?: string
  value: number
  convertedValue?: number
  precision?: number
  factor?: number
}

export const DetailTab: React.FC<SaleTabProps> = () => {
  const { sale } = useSaleContext()
  const { styles } = useDetailTabStyles()

  if (!sale) return null

  const details = ((sale as any).details || []) as DetailItem[]
  const priceRules = ((sale as any).priceRule || []) as PriceRule[]
  const summary = ((sale as any).summary || []) as SummaryItem[]

  // Determine if we need converted currency columns
  const showConverted = sale.currency?.id !== (sale as any).baseCurrency?.id
  const currencyCode = getCurrencyCode(sale.currency)
  const baseCurrencyCode = getCurrencyCode((sale as any).baseCurrency)

  // Items table columns
  const itemColumns: Array<ColumnType<DetailItem>> = [
    {
      title: 'Product',
      dataIndex: 'productName',
      key: 'productName',
      width: '30%'
    },
    {
      title: 'Price (excl.)',
      dataIndex: 'priceNet',
      key: 'priceNet',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, baseCurrencyCode)
    },
    {
      title: 'Price (incl.)',
      dataIndex: 'price',
      key: 'price',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, baseCurrencyCode)
    },
    {
      title: 'Quantity',
      dataIndex: 'quantity',
      key: 'quantity',
      width: 100,
      align: 'right'
    },
    {
      title: 'Unit',
      dataIndex: 'unit',
      key: 'unit',
      width: 100,
      align: 'right',
      render: (value) => value || '--'
    },
    {
      title: 'Total (excl.)',
      key: 'totalNet',
      width: 150,
      align: 'right',
      render: (_, record) => formatCurrency(record.total - record.totalTax, baseCurrencyCode)
    },
    {
      title: 'Total (incl.)',
      dataIndex: 'total',
      key: 'total',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, baseCurrencyCode)
    }
  ]

  // Add converted columns if needed
  if (showConverted) {
    itemColumns.splice(2, 0, {
      title: `Price (excl.) ${currencyCode || ''}`,
      dataIndex: 'convertedPriceNet',
      key: 'convertedPriceNet',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, currencyCode)
    })

    itemColumns.splice(4, 0, {
      title: `Price (incl.) ${currencyCode || ''}`,
      dataIndex: 'convertedPrice',
      key: 'convertedPrice',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, currencyCode)
    })

    itemColumns.splice(8, 0, {
      title: `Total (excl.) ${currencyCode || ''}`,
      key: 'convertedTotalNet',
      width: 150,
      align: 'right',
      render: (_, record) => formatCurrency(
        (record.convertedTotal || 0) - (record.convertedTotalTax || 0),
        currencyCode
      )
    })

    itemColumns.splice(10, 0, {
      title: `Total (incl.) ${currencyCode || ''}`,
      dataIndex: 'convertedTotal',
      key: 'convertedTotal',
      width: 150,
      align: 'right',
      render: (value) => formatCurrency(value, currencyCode)
    })
  }

  // Price rules table columns
  const priceRuleColumns: Array<ColumnType<PriceRule>> = [
    {
      dataIndex: 'name',
      key: 'name',
      render: (value, record) => {
        if (record.code) {
          return (
            <>
              {value} (<em>{record.code}</em>)
            </>
          )
        }
        return value
      }
    },
    {
      dataIndex: 'discount',
      key: 'discount',
      width: 150,
      align: 'right',
      render: (value) => (
        <span style={{ fontWeight: 'bold' }}>
          {formatCurrency(value, baseCurrencyCode)}
        </span>
      )
    }
  ]

  if (showConverted) {
    priceRuleColumns.push({
      dataIndex: 'convertedDiscount',
      key: 'convertedDiscount',
      width: 150,
      align: 'right',
      render: (value) => (
        <span style={{ fontWeight: 'bold' }}>
          {formatCurrency(value, currencyCode)}
        </span>
      )
    })
  }

  // Summary table columns
  const summaryColumns: Array<ColumnType<SummaryItem>> = [
    {
      dataIndex: 'key',
      key: 'key',
      align: 'right',
      render: (value, record) => {
        const text = record.text || value
        return <span style={{ fontWeight: 'bold' }}>{text}</span>
      }
    },
    {
      dataIndex: 'value',
      key: 'value',
      width: 150,
      align: 'right',
      render: (value, record) => {
        const formatted = formatCurrency(value, baseCurrencyCode)
        return <span style={{ fontWeight: 'bold' }}>{formatted}</span>
      }
    }
  ]

  if (showConverted) {
    summaryColumns.push({
      dataIndex: 'convertedValue',
      key: 'convertedValue',
      width: 150,
      align: 'right',
      render: (value) => {
        const formatted = formatCurrency(value, currencyCode)
        return <span style={{ fontWeight: 'bold' }}>{formatted}</span>
      }
    })
  }

  return (
    <div className={styles.container}>
      {/* Items Table with Summary */}
      <Card title="Items" className={styles.card}>
        <Table
          dataSource={details}
          columns={itemColumns}
          rowKey="id"
          pagination={false}
          className={styles.table}
          size="small"
        />

        {/* Price Rules Section */}
        {priceRules.length > 0 && (
          <div className={styles.priceRulesSection}>
            <Table
              dataSource={priceRules}
              columns={priceRuleColumns}
              pagination={false}
              className={styles.priceRulesTable}
              size="small"
              showHeader={false}
            />
          </div>
        )}

        {/* Summary Section */}
        <div className={styles.summarySection}>
          <Table
            dataSource={summary}
            columns={summaryColumns}
            pagination={false}
            className={styles.summaryTable}
            size="small"
            showHeader={false}
          />
        </div>
      </Card>
    </div>
  )
}

const useDetailTabStyles = createStyles(({ css, token }) => ({
  container: css`
    padding: 20px;
  `,
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }

    .ant-card-body {
      padding: 0;
    }
  `,
  table: css`
    .ant-table-thead > tr > th {
      background: ${token.colorBgContainer};
      font-weight: 600;
    }

    .ant-table {
      margin-bottom: 0;
    }
  `,
  priceRulesSection: css`
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid ${token.colorBorderSecondary};
  `,
  priceRulesTable: css`
    .ant-table-tbody > tr > td {
      border-bottom: none;
      padding: 8px 16px;
    }
  `,
  summarySection: css`
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid ${token.colorBorderSecondary};
  `,
  summaryTable: css`
    .ant-table-tbody > tr > td {
      border-bottom: none;
      padding: 8px 16px;
    }
  `
}))
