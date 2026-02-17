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
import { Table, Card, Tag, theme } from 'antd'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import { useTableCardStyles } from '../styles/useTableCardStyles'
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
  const { t } = useTranslation()
  const { sale } = useSaleContext()
  const { styles: sharedStyles } = useTableCardStyles()
  const { styles: localStyles } = useDetailTabStyles()
  const styles = { ...sharedStyles, ...localStyles }

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
      title: t('coreshop_product', { defaultValue: 'Product' }),
      dataIndex: 'productName',
      key: 'productName',
      ellipsis: true,
      render: (value) => <span style={{ fontWeight: 500 }}>{value}</span>
    },
    {
      title: t('coreshop_price_without_tax', { defaultValue: 'Price (excl.)' }),
      dataIndex: 'priceNet',
      key: 'priceNet',
      width: 110,
      align: 'right',
      render: (value) => <span className={styles.monoNum}>{formatCurrency(value, baseCurrencyCode)}</span>
    },
    {
      title: t('coreshop_price_with_tax', { defaultValue: 'Price (incl.)' }),
      dataIndex: 'price',
      key: 'price',
      width: 110,
      align: 'right',
      render: (value) => <span className={styles.monoNum}>{formatCurrency(value, baseCurrencyCode)}</span>
    },
    {
      title: t('coreshop_quantity', { defaultValue: 'Qty' }),
      dataIndex: 'quantity',
      key: 'quantity',
      width: 50,
      align: 'center',
      render: (value) => <span style={{ fontWeight: 600 }}>{value}</span>
    },
    {
      title: t('coreshop_unit', { defaultValue: 'Unit' }),
      dataIndex: 'unit',
      key: 'unit',
      width: 70,
      align: 'center',
      render: (value) => value || '\u2013'
    },
    {
      title: t('coreshop_total_without_tax', { defaultValue: 'Total (excl.)' }),
      key: 'totalNet',
      width: 110,
      align: 'right',
      render: (_, record) => <span className={styles.monoNum}>{formatCurrency(record.total - record.totalTax, baseCurrencyCode)}</span>
    },
    {
      title: t('coreshop_total', { defaultValue: 'Total' }),
      dataIndex: 'total',
      key: 'total',
      width: 110,
      align: 'right',
      render: (value) => <strong className={styles.monoNum}>{formatCurrency(value, baseCurrencyCode)}</strong>
    }
  ]

  // Add converted columns if needed
  if (showConverted) {
    itemColumns.splice(2, 0, {
      title: `${t('coreshop_converted_price_without_tax', { defaultValue: 'Conv. Price (excl.)' })} ${currencyCode || ''}`.trim(),
      dataIndex: 'convertedPriceNet',
      key: 'convertedPriceNet',
      width: 150,
      align: 'right',
      render: (value) => <span className={styles.monoNum}>{formatCurrency(value, currencyCode)}</span>
    })

    itemColumns.splice(4, 0, {
      title: `${t('coreshop_converted_price_with_tax', { defaultValue: 'Conv. Price (incl.)' })} ${currencyCode || ''}`.trim(),
      dataIndex: 'convertedPrice',
      key: 'convertedPrice',
      width: 150,
      align: 'right',
      render: (value) => <span className={styles.monoNum}>{formatCurrency(value, currencyCode)}</span>
    })

    itemColumns.splice(8, 0, {
      title: `${t('coreshop_converted_total_without_tax', { defaultValue: 'Conv. Total (excl.)' })} ${currencyCode || ''}`.trim(),
      key: 'convertedTotalNet',
      width: 150,
      align: 'right',
      render: (_, record) => <span className={styles.monoNum}>{formatCurrency(
        (record.convertedTotal || 0) - (record.convertedTotalTax || 0),
        currencyCode
      )}</span>
    })

    itemColumns.splice(10, 0, {
      title: `${t('coreshop_converted_total', { defaultValue: 'Conv. Total' })} ${currencyCode || ''}`.trim(),
      dataIndex: 'convertedTotal',
      key: 'convertedTotal',
      width: 150,
      align: 'right',
      render: (value) => <span className={styles.monoNum}>{formatCurrency(value, currencyCode)}</span>
    })
  }

  // Price rules table columns
  const priceRuleColumns: Array<ColumnType<PriceRule>> = [
    {
      dataIndex: 'name',
      key: 'name',
      render: (value, record) => {
        return (
          <span>
            {value}
            {record.code && (
              <Tag color="blue" style={{ marginLeft: 8 }}>{record.code}</Tag>
            )}
          </span>
        )
      }
    },
    {
      dataIndex: 'discount',
      key: 'discount',
      width: 150,
      align: 'right',
      render: (value) => (
        <span className={styles.discountValue}>
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
        <span className={styles.discountValue}>
          {formatCurrency(value, currencyCode)}
        </span>
      )
    })
  }

  // Format summary key to readable label
  const formatSummaryKey = (key: string, text?: string): string => {
    if (text) return text
    return key
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ')
  }

  const { token } = theme.useToken()

  const isTotalRow = (key: string) => key === 'total' || key === 'payment_total'

  // Sort summary: regular items first, then totals last
  const sortedSummary = React.useMemo(() => {
    const regular = summary.filter(item => !isTotalRow(item.key))
    const totals = summary.filter(item => isTotalRow(item.key))
    return [...regular, ...totals]
  }, [summary])

  return (
    <div className={styles.container}>
      <Card title={t('coreshop_products', { defaultValue: 'Products' })} className={styles.card}>
        <Table
          dataSource={details}
          columns={itemColumns}
          rowKey="id"
          pagination={false}
          className={styles.table}
          size="small"
          scroll={{ x: 'max-content' }}
        />

        {/* Price Rules Section */}
        {priceRules.length > 0 && (
          <div className={styles.priceRulesSection}>
            <div className={styles.sectionLabel}>{t('coreshop_price_rules', { defaultValue: 'Price Rules' })}</div>
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
          <div className={styles.summaryWrapper}>
            {sortedSummary.map((item) => {
              const isTotal = isTotalRow(item.key)
              const label = formatSummaryKey(item.key, item.text)
              return (
                <div key={item.key} className={`${styles.summaryRow} ${isTotal ? styles.summaryRowTotal : ''}`}>
                  <span className={styles.summaryLabel} style={isTotal ? { fontWeight: 600, color: token.colorText } : undefined}>
                    {label}
                  </span>
                  <span className={`${styles.summaryValue} ${styles.monoNum}`} style={isTotal ? { fontWeight: 700, fontSize: 15 } : undefined}>
                    {formatCurrency(item.value, baseCurrencyCode)}
                  </span>
                  {showConverted && (
                    <span className={`${styles.summaryValue} ${styles.monoNum}`} style={isTotal ? { fontWeight: 700, fontSize: 15 } : undefined}>
                      {formatCurrency(item.convertedValue, currencyCode)}
                    </span>
                  )}
                </div>
              )
            })}
          </div>
        </div>
      </Card>
    </div>
  )
}

const useDetailTabStyles = createStyles(({ css, token }) => ({
  container: css``,
  monoNum: css`
    font-variant-numeric: tabular-nums;
  `,
  priceRulesSection: css`
    padding: 12px 16px;
    border-top: 1px solid ${token.colorBorderSecondary};
    background: ${token.colorBgLayout};
  `,
  sectionLabel: css`
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: ${token.colorTextTertiary};
    margin-bottom: 8px;
  `,
  priceRulesTable: css`
    .ant-table-tbody > tr > td {
      border-bottom: none;
      padding: 6px 0;
      background: transparent;
    }

    .ant-table {
      background: transparent;
    }
  `,
  discountValue: css`
    font-weight: 600;
    color: ${token.colorError};
    font-variant-numeric: tabular-nums;
  `,
  summarySection: css`
    padding: 20px 24px;
    background: ${token.colorBgLayout};
    border-top: 1px solid ${token.colorBorderSecondary};
    display: flex;
    justify-content: flex-end;
  `,
  summaryWrapper: css`
    min-width: 300px;
    max-width: 400px;
    width: 100%;
  `,
  summaryRow: css`
    display: flex;
    align-items: baseline;
    padding: 6px 0;
    border-bottom: 1px dashed ${token.colorBorderSecondary};

    &:last-child {
      border-bottom: none;
    }
  `,
  summaryRowTotal: css`
    border-bottom: none;
    border-top: 2px solid ${token.colorBorder};
    padding-top: 10px;
    margin-top: 4px;
  `,
  summaryLabel: css`
    flex: 1;
    text-align: right;
    padding-right: 20px;
    font-size: 13px;
    color: ${token.colorTextSecondary};
  `,
  summaryValue: css`
    text-align: right;
    font-size: 13px;
    font-weight: 500;
    color: ${token.colorText};
    min-width: 100px;
  `
}))
