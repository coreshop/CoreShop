/**
 * CoreShop OrderBundle Payment Tab
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
import { Table, Button, Card, Empty, Modal } from 'antd'
import { createStyles } from 'antd-style'
import { PlusOutlined, FolderOpenOutlined } from '@ant-design/icons'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps } from '../registry'
import { StateChangeModal, CreatePaymentModal } from '../components'
import { paymentEvents, PAYMENT_EVENTS } from '../events/PaymentEvents'

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

interface Payment {
  id: number
  datePayment: number
  provider: string
  paymentNumber: string
  details: any[]
  amount: number
  stateInfo: StateInfo
  transitions: Transition[]
}

export const PaymentTab: React.FC<SaleTabProps> = ({ sale, onReload }) => {
  const { styles } = usePaymentTabStyles()
  const [selectedPayment, setSelectedPayment] = React.useState<Payment | null>(null)
  const [isDetailModalOpen, setIsDetailModalOpen] = React.useState(false)
  const [stateChangePayment, setStateChangePayment] = React.useState<Payment | null>(null)
  const [createPaymentOpen, setCreatePaymentOpen] = React.useState(false)

  const payments = ((sale as any).payments || []) as Payment[]

  // Listen for create payment events from toolbar
  React.useEffect(() => {
    const handleCreatePayment = () => {
      setCreatePaymentOpen(true)
    }

    paymentEvents.on(PAYMENT_EVENTS.CREATE_PAYMENT, handleCreatePayment)

    return () => {
      paymentEvents.off(PAYMENT_EVENTS.CREATE_PAYMENT, handleCreatePayment)
    }
  }, [])

  // Handle open payment detail
  const handleOpenPaymentDetail = (payment: Payment) => {
    setSelectedPayment(payment)
    setIsDetailModalOpen(true)
  }

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
    return new Date(date * 1000).toLocaleDateString('de-DE')
  }

  const columns: Array<ColumnType<Payment>> = [
    {
      title: 'Transaction Number',
      dataIndex: 'paymentNumber',
      key: 'paymentNumber',
      width: 200
    },
    {
      title: 'Date',
      dataIndex: 'datePayment',
      key: 'datePayment',
      width: 120,
      render: (date) => formatDate(date)
    },
    {
      title: 'Payment Provider',
      dataIndex: 'provider',
      key: 'provider',
      width: 150,
      render: (provider) => provider || '-'
    },
    {
      title: 'Amount',
      dataIndex: 'amount',
      key: 'amount',
      width: 120,
      align: 'right',
      render: (amount) => formatCurrency(amount)
    },
    {
      title: '',
      key: 'state',
      width: 100,
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
            onClick={() => {
              if (hasTransitions) {
                setStateChangePayment(record)
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
          title="Open Payment Details"
          onClick={() => handleOpenPaymentDetail(record)}
        />
      )
    }
  ]

  return (
    <>
      <Card
        title="Payment(s)"
        className={styles.card}
        extra={
          <Button
            type="text"
            icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
            title="Add Payment"
          />
        }
      >
        {payments.length === 0 ? (
          <Empty description="No payments recorded" image={Empty.PRESENTED_IMAGE_SIMPLE} />
        ) : (
          <Table
            dataSource={payments}
            columns={columns}
            rowKey="id"
            pagination={false}
            className={styles.table}
            size="small"
          />
        )}
      </Card>

      {/* Payment Detail Modal */}
      <Modal
        title="Payment Details"
        open={isDetailModalOpen}
        onCancel={() => {
          setIsDetailModalOpen(false)
          setSelectedPayment(null)
        }}
        footer={[
          <Button key="ok" type="primary" onClick={() => setIsDetailModalOpen(false)}>
            OK
          </Button>
        ]}
        width={600}
      >
        {selectedPayment && (
          <div className={styles.detailContent}>
            <div className={styles.detailRow}>
              <div className={styles.detailLabel}>Date:</div>
              <div className={styles.detailValue}>
                {new Date(selectedPayment.datePayment * 1000).toLocaleString('de-DE', {
                  day: '2-digit',
                  month: '2-digit',
                  year: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </div>
            </div>

            <div className={styles.detailRow}>
              <div className={styles.detailLabel}>Transaction Number:</div>
              <div className={styles.detailValue}>{selectedPayment.paymentNumber}</div>
            </div>

            <div className={styles.detailRow}>
              <div className={styles.detailLabel}>Payment Provider:</div>
              <div className={styles.detailValue}>{selectedPayment.provider}</div>
            </div>

            <div className={styles.detailRow}>
              <div className={styles.detailLabel}>Amount:</div>
              <div className={styles.detailValue}>{selectedPayment.amount / 100}</div>
            </div>

            {/* Details Table */}
            {selectedPayment.details && selectedPayment.details.length > 0 && (
              <>
                <div className={styles.detailsHeader}>Details</div>
                <Table
                  dataSource={selectedPayment.details}
                  columns={[
                    {
                      title: 'Name',
                      dataIndex: 'name',
                      key: 'name',
                      width: '30%'
                    },
                    {
                      title: 'Value',
                      dataIndex: 'value',
                      key: 'value',
                      width: '70%',
                      render: (value) => (
                        <div style={{ whiteSpace: 'normal', wordWrap: 'break-word' }}>
                          {value}
                        </div>
                      )
                    }
                  ]}
                  pagination={false}
                  size="small"
                  rowKey={(record, index) => index?.toString() || '0'}
                  expandable={{
                    expandedRowRender: (record) => (
                      <div className={styles.detailRowBody}>
                        {record.detail}
                      </div>
                    ),
                    rowExpandable: (record) => !!record.detail
                  }}
                />
              </>
            )}
          </div>
        )}
      </Modal>

      {/* State Change Modal */}
      {stateChangePayment && (
        <StateChangeModal
          open={true}
          title="Change Payment State"
          description="Select a transition to apply to this payment"
          transitions={stateChangePayment.transitions}
          url="/pimcore-studio/api/coreshop/order-payment/update-payment-state"
          id={stateChangePayment.id}
          onSuccess={() => {
            setStateChangePayment(null)
            onReload()
          }}
          onCancel={() => setStateChangePayment(null)}
        />
      )}

      {/* Create Payment Modal */}
      {createPaymentOpen && (
        <CreatePaymentModal
          open={true}
          orderId={(sale as any).id}
          unpaidAmount={(sale as any).totalUnpaid}
          onSuccess={() => {
            setCreatePaymentOpen(false)
            onReload()
          }}
          onCancel={() => setCreatePaymentOpen(false)}
        />
      )}
    </>
  )
}

const usePaymentTabStyles = createStyles(({ css, token }) => ({
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
  `,
  detailContent: css`
    padding: 16px 0;
  `,
  detailRow: css`
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid ${token.colorBorder};

    &:last-of-type {
      border-bottom: none;
    }
  `,
  detailLabel: css`
    width: 180px;
    font-weight: 500;
    color: ${token.colorTextSecondary};
    flex-shrink: 0;
  `,
  detailValue: css`
    flex: 1;
    color: ${token.colorText};
  `,
  detailsHeader: css`
    font-size: 16px;
    font-weight: 600;
    margin: 24px 0 12px 0;
    color: ${token.colorText};
  `,
  detailRowBody: css`
    padding: 12px;
    background: ${token.colorBgLayout};
    white-space: normal;
    word-wrap: break-word;
  `
}))
