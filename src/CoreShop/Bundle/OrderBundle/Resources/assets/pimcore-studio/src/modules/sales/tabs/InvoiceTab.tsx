/**
 * CoreShop OrderBundle Invoice Tab
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
import { Table, Button, Card, Empty } from 'antd'
import { createStyles } from 'antd-style'
import { FolderOpenOutlined, PlusOutlined } from '@ant-design/icons'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps } from '../registry'
import { StateChangeModal, InvoiceDetailModal, CreateInvoiceModal } from '../components'
import { invoiceEvents, INVOICE_EVENTS } from '../events/InvoiceEvents'

interface StateInfo {
  label: string
  state: string
  color: string
}

interface Transition {
  label: string
  transition: string
  color: string
}

interface InvoiceItem {
  _itemName: string
  quantity: number
}

interface Invoice {
  id: number
  invoiceDate: number
  invoiceNumber: string
  totalNet: number
  totalGross: number
  stateInfo: StateInfo
  transitions: Transition[]
  items: InvoiceItem[]
}

export const InvoiceTab: React.FC<SaleTabProps> = ({ sale, onReload }) => {
  const { styles } = useInvoiceTabStyles()
  const [stateChangeInvoice, setStateChangeInvoice] = React.useState<Invoice | null>(null)
  const [detailInvoice, setDetailInvoice] = React.useState<Invoice | null>(null)
  const [createInvoiceOpen, setCreateInvoiceOpen] = React.useState(false)

  const invoices = ((sale as any).invoices || []) as Invoice[]

  // Listen for create invoice events from toolbar
  React.useEffect(() => {
    const handleCreateInvoice = () => {
      setCreateInvoiceOpen(true)
    }

    invoiceEvents.on(INVOICE_EVENTS.CREATE_INVOICE, handleCreateInvoice)

    return () => {
      invoiceEvents.off(INVOICE_EVENTS.CREATE_INVOICE, handleCreateInvoice)
    }
  }, [])

  // Format currency
  const formatCurrency = (amount?: number) => {
    if (amount === undefined) return '-'

    // Handle currency as object or string
    const currencyCode = typeof sale.currency === 'object' && sale.currency?.isoCode
      ? sale.currency.isoCode
      : typeof sale.currency === 'string'
        ? sale.currency
        : 'EUR'

    return new Intl.NumberFormat('de-DE', {
      style: 'currency',
      currency: currencyCode
    }).format(amount / 100) // Divide by 100 because amounts are in cents
  }

  // Format date
  const formatDate = (date?: number) => {
    if (!date) return '-'
    return new Date(date * 1000).toLocaleString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const columns: Array<ColumnType<Invoice>> = [
    {
      title: 'Date',
      dataIndex: 'invoiceDate',
      key: 'invoiceDate',
      width: 180,
      render: (date) => formatDate(date)
    },
    {
      title: 'Total (excl.)',
      dataIndex: 'totalNet',
      key: 'totalNet',
      width: 150,
      align: 'right',
      render: (amount) => formatCurrency(amount)
    },
    {
      title: 'Total',
      dataIndex: 'totalGross',
      key: 'totalGross',
      width: 150,
      align: 'right',
      render: (amount) => <strong>{formatCurrency(amount)}</strong>
    },
    {
      title: '',
      key: 'state',
      width: 150,
      render: (_, record) => {
        const hasTransitions = record.transitions && record.transitions.length > 0
        return (
          <Button
            style={{
              backgroundColor: record.stateInfo.color,
              borderColor: record.stateInfo.color,
              color: '#fff',
              cursor: hasTransitions ? 'pointer' : 'default'
            }}
            size="small"
            icon={hasTransitions ? <PlusOutlined style={{ fontSize: 10 }} /> : undefined}
            onClick={() => {
              if (hasTransitions) {
                setStateChangeInvoice(record)
              }
            }}
            disabled={!hasTransitions}
          >
            {record.stateInfo.label}
          </Button>
        )
      }
    },
    {
      title: '',
      key: 'actions',
      width: 80,
      render: (_, record) => (
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          size="small"
          title="Open Invoice Details"
          onClick={() => setDetailInvoice(record)}
        />
      )
    }
  ]

  // Calculate totals
  const totalGross = invoices.reduce((sum, invoice) => sum + (invoice.totalGross || 0), 0)
  const totalNet = invoices.reduce((sum, invoice) => sum + (invoice.totalNet || 0), 0)

  return (
    <>
      <Card
        title="Invoices"
        className={styles.card}
        extra={
          (sale as any).invoiceCreationAllowed && (
            <Button
              type="text"
              icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
              title="Add Invoice"
              onClick={() => setCreateInvoiceOpen(true)}
            />
          )
        }
      >
        {invoices.length === 0 ? (
          <Empty description="No invoices generated" image={Empty.PRESENTED_IMAGE_SIMPLE} />
        ) : (
          <Table
            dataSource={invoices}
            columns={columns}
            rowKey="id"
            pagination={false}
            className={styles.table}
            size="small"
          />
        )}
      </Card>

      {/* State Change Modal */}
      {stateChangeInvoice && (
        <StateChangeModal
          open={true}
          title="Change Invoice State"
          description="Select a transition to apply to this invoice"
          transitions={stateChangeInvoice.transitions}
          url="/pimcore-studio/api/coreshop/order-invoice/update-invoice-state"
          id={stateChangeInvoice.id}
          onSuccess={() => {
            setStateChangeInvoice(null)
            onReload()
          }}
          onCancel={() => setStateChangeInvoice(null)}
        />
      )}

      {/* Invoice Detail Modal */}
      {detailInvoice && (
        <InvoiceDetailModal
          open={true}
          invoice={detailInvoice}
          currencyCode={typeof sale.currency === 'object' && sale.currency?.isoCode
            ? sale.currency.isoCode
            : typeof sale.currency === 'string'
              ? sale.currency
              : 'EUR'}
          onClose={() => setDetailInvoice(null)}
        />
      )}

      {/* Create Invoice Modal */}
      {createInvoiceOpen && (
        <CreateInvoiceModal
          open={true}
          orderId={(sale as any).id}
          currencyCode={
            typeof sale.currency === 'object' && sale.currency?.isoCode
              ? sale.currency.isoCode
              : typeof sale.currency === 'string'
                ? sale.currency
                : 'EUR'
          }
          onSuccess={() => {
            setCreateInvoiceOpen(false)
            onReload()
          }}
          onCancel={() => setCreateInvoiceOpen(false)}
        />
      )}
    </>
  )
}

const useInvoiceTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }
  `,
  table: css`
    .ant-table-thead > tr > th {
      background: ${token.colorBgContainer};
      font-weight: 600;
    }
  `
}))
