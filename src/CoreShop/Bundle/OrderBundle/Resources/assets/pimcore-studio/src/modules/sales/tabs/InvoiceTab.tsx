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
import { useTranslation } from 'react-i18next'
import { formatDateTime, formatCurrency, getCurrencyCode } from '@coreshop/pimcore/src/utils'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps } from '../registry'
import { StateChangeModal, InvoiceDetailModal, CreateInvoiceModal, CreateInvoiceButton } from '../components'
import { useSaleContext } from '../context/SaleActionsContext'

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

export const InvoiceTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const { sale, onReload, isActionOpen, openAction, closeAction, buttonRegistry } = useSaleContext()
  const { styles } = useInvoiceTabStyles()
  const [stateChangeInvoice, setStateChangeInvoice] = React.useState<Invoice | null>(null)
  const [detailInvoice, setDetailInvoice] = React.useState<Invoice | null>(null)

  if (!sale) return null

  const invoices = ((sale as any).invoices || []) as Invoice[]

  // Register button in toolbar
  React.useEffect(() => {
    if ((sale as any)?.invoiceCreationAllowed) {
      buttonRegistry.add('createInvoice', CreateInvoiceButton, 30)
      return () => buttonRegistry.remove('createInvoice')
    }
  }, [buttonRegistry, sale])

  const currencyCode = getCurrencyCode(sale.currency)

  const columns: Array<ColumnType<Invoice>> = [
    {
      title: t('coreshop_date', { defaultValue: 'Date' }),
      dataIndex: 'invoiceDate',
      key: 'invoiceDate',
      width: 160,
      render: (date) => formatDateTime(date)
    },
    {
      title: t('coreshop_total_without_tax', { defaultValue: 'Total (excl.)' }),
      dataIndex: 'totalNet',
      key: 'totalNet',
      width: 130,
      align: 'right',
      render: (amount) => formatCurrency(amount, currencyCode)
    },
    {
      title: t('coreshop_total', { defaultValue: 'Total' }),
      dataIndex: 'totalGross',
      key: 'totalGross',
      width: 130,
      align: 'right',
      render: (amount) => <strong>{formatCurrency(amount, currencyCode)}</strong>
    },
    {
      title: t('coreshop_status', { defaultValue: 'Status' }),
      key: 'state',
      width: 120,
      align: 'center',
      render: (_, record) => {
        const hasTransitions = record.transitions && record.transitions.length > 0
        return (
          <Button
            style={{
              backgroundColor: record.stateInfo.color,
              borderColor: record.stateInfo.color,
              color: '#fff',
              cursor: hasTransitions ? 'pointer' : 'default',
              minWidth: 90
            }}
            size="small"
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
      width: 50,
      align: 'center',
      render: (_, record) => (
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          size="small"
          title={t('coreshop_open_order_invoice', { defaultValue: 'Open Invoice ({0})' }).replace('{0}', record.invoiceNumber)}
          onClick={() => setDetailInvoice(record)}
        />
      )
    }
  ]

  return (
    <>
      <Card
        title={t('coreshop_invoices', { defaultValue: 'Invoices' })}
        className={styles.card}
        extra={
          (sale as any).invoiceCreationAllowed && (
            <Button
              type="text"
              icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
              title={t('coreshop_invoice_create_short', { defaultValue: 'Create Invoice' })}
              onClick={() => openAction('createInvoice')}
            />
          )
        }
      >
        {invoices.length === 0 ? (
          <Empty description={t('coreshop_invoice_no_items', { defaultValue: 'No Invoice able Items found' })} image={Empty.PRESENTED_IMAGE_SIMPLE} />
        ) : (
          <Table
            dataSource={invoices}
            columns={columns}
            rowKey="id"
            pagination={false}
            className={styles.table}
            size="small"
            scroll={{ y: 300 }}
          />
        )}
      </Card>

      {/* State Change Modal */}
      {stateChangeInvoice && (
        <StateChangeModal
          open={true}
          title={t('coreshop_change_state', { defaultValue: 'Change State' })}
          description={t('coreshop_change_state_description', { defaultValue: 'Click on a Button below to change the current State' })}
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
      {isActionOpen('createInvoice') && (
        <CreateInvoiceModal
          open={true}
          orderId={sale.id}
          currencyCode={sale.currency?.isoCode}
          onSuccess={() => {
            closeAction('createInvoice')
            onReload()
          }}
          onCancel={() => closeAction('createInvoice')}
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
