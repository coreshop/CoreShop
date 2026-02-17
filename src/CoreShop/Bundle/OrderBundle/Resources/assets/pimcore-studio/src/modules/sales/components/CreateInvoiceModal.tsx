/**
 * CoreShop OrderBundle Create Invoice Modal (Base Version)
 *
 * This is the base version without payment provider selection.
 * CoreBundle can extend this via ModalFieldExtensionRegistry.
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
import { Modal, Form, InputNumber, Table, Tabs } from 'antd'
import { createStyles } from 'antd-style'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { formatCurrency } from '@coreshop/pimcore/src/utils'
import { container } from '@pimcore/studio-ui-bundle'
import type { ColumnType } from 'antd/es/table'
import { ModalFieldExtensionRegistry } from '../extensions'
import { extensionServiceIds } from '../extensions/service-ids'
import { getErrorMessage } from '@coreshop/resource/src/entities'

interface InvoiceItem {
  orderItemId: number
  name: string
  price: number
  quantity: number
  quantityInvoiced: number
  maxToInvoice: number
  toInvoice: number
  tax: number
  total: number
}

export interface CreateInvoiceModalProps {
  open: boolean
  orderId: number
  currencyCode: string
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Create Invoice Modal (Base Version)
 *
 * Pattern from ExtJS: /order/invoice.js (OrderBundle)
 * Note: CoreBundle can extend this by adding payment provider selection via ModalFieldExtensionRegistry
 */
export const CreateInvoiceModal: React.FC<CreateInvoiceModalProps> = ({
  open,
  orderId,
  currencyCode,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useCreateInvoiceModalStyles()
  const [form] = Form.useForm()
  const [items, setItems] = React.useState<InvoiceItem[]>([])
  const [loading, setLoading] = React.useState(false)
  const [loadingItems, setLoadingItems] = React.useState(false)

  // Get additional fields from extension registry
  const extensionRegistry = React.useMemo(
    () => container.get<ModalFieldExtensionRegistry>(extensionServiceIds.modalFieldExtensionRegistry),
    []
  )
  const additionalFields = React.useMemo(
    () => extensionRegistry.getFields('create-invoice', { form, orderId, currencyCode }),
    [extensionRegistry, form, orderId, currencyCode]
  )

  // Load invoiceable items
  React.useEffect(() => {
    if (!open) return

    const loadItems = async () => {
      setLoadingItems(true)
      try {
        const response = await fetch(`/pimcore-studio/api/coreshop/order-invoice/get-invoice-able-items?id=${orderId}`)
        const data = await response.json()

        if (data.success && data.items && data.items.length > 0) {
          setItems(data.items)
        } else {
          void messageApi.info(t('coreshop_invoice_no_items', { defaultValue: 'No invoiceable items found' }))
          onCancel()
        }
      } catch (error) {
        void messageApi.error(getErrorMessage(error, t('coreshop_invoice_load_items_error', { defaultValue: 'Failed to load invoiceable items' })))
        onCancel()
      } finally {
        setLoadingItems(false)
      }
    }

    void loadItems()
  }, [open, orderId, onCancel, t])

  // Handle quantity change
  const handleQuantityChange = (index: number, value: number | null) => {
    if (value === null) return

    const newItems = [...items]
    const item = newItems[index]

    // Ensure quantity is within valid range
    const clampedValue = Math.min(Math.max(0, value), item.maxToInvoice)
    item.toInvoice = clampedValue

    // Recalculate total
    item.total = item.price * clampedValue + item.tax * clampedValue

    setItems(newItems)
  }

  // Calculate totals
  const calculateTotals = () => {
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.toInvoice), 0)
    const tax = items.reduce((sum, item) => sum + (item.tax * item.toInvoice), 0)
    const total = subtotal + tax

    return { subtotal, tax, total }
  }

  const totals = calculateTotals()

  // Handle save
  const handleSave = async () => {
    try {
      await form.validateFields()
      setLoading(true)

      const formValues = form.getFieldsValue()

      const payload = {
        id: orderId,
        items: items
          .filter(item => item.toInvoice > 0)
          .map(item => ({
            orderItemId: item.orderItemId,
            quantity: item.toInvoice
          })),
        ...formValues
      }

      const response = await fetch('/pimcore-studio/api/coreshop/order-invoice/create-invoice', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      const result = await response.json()

      if (result.success) {
        void messageApi.success(t('coreshop_invoice_create_success', { defaultValue: 'Invoice created successfully' }))
        form.resetFields()
        onSuccess()
      } else {
        void messageApi.error(result.message || t('coreshop_invoice_create_error', { defaultValue: 'Failed to create invoice' }))
      }
    } catch (error) {
      void messageApi.error(getErrorMessage(error, t('coreshop_invoice_create_error', { defaultValue: 'Failed to create invoice' })))
    } finally {
      setLoading(false)
    }
  }

  const columns: Array<ColumnType<InvoiceItem>> = [
    {
      title: t('coreshop_product', { defaultValue: 'Product' }),
      dataIndex: 'name',
      key: 'name',
      width: 250
    },
    {
      title: t('coreshop_price', { defaultValue: 'Price' }),
      dataIndex: 'price',
      key: 'price',
      width: 120,
      align: 'right',
      render: (price) => formatCurrency(price, currencyCode)
    },
    {
      title: t('coreshop_quantity', { defaultValue: 'Quantity' }),
      dataIndex: 'quantity',
      key: 'quantity',
      width: 100,
      align: 'center'
    },
    {
      title: t('coreshop_invoiced_quantity', { defaultValue: 'Invoiced Quantity' }),
      dataIndex: 'quantityInvoiced',
      key: 'quantityInvoiced',
      width: 150,
      align: 'center'
    },
    {
      title: t('coreshop_to_invoice', { defaultValue: 'To Invoice' }),
      dataIndex: 'toInvoice',
      key: 'toInvoice',
      width: 120,
      align: 'center',
      render: (_, record, index) => (
        <InputNumber
          min={0}
          max={record.maxToInvoice}
          value={record.toInvoice}
          onChange={(value) => handleQuantityChange(index, value)}
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_tax', { defaultValue: 'Tax' }),
      dataIndex: 'tax',
      key: 'tax',
      width: 100,
      align: 'right',
      render: (tax, record) => formatCurrency(tax * record.toInvoice, currencyCode)
    },
    {
      title: t('coreshop_total', { defaultValue: 'Total' }),
      dataIndex: 'total',
      key: 'total',
      width: 120,
      align: 'right',
      render: (_, record) => <strong>{formatCurrency(record.price * record.toInvoice + record.tax * record.toInvoice, currencyCode)}</strong>
    }
  ]

  return (
    <Modal
      title={`${t('coreshop_invoice_create_new', { defaultValue: 'Create Invoice for Order' })} (${orderId})`}
      open={open}
      onCancel={onCancel}
      onOk={handleSave}
      okText={t('coreshop_save', { defaultValue: 'Save' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      width={1200}
      confirmLoading={loading}
      className={styles.modal}
    >
      <Form
        form={form}
        layout="vertical"
        className={styles.form}
      >
        <Tabs
          defaultActiveKey="invoice"
          items={[
            {
              key: 'invoice',
              label: t('coreshop_invoice', { defaultValue: 'Invoice' }),
              children: (
                <div className={styles.content}>
                  {/* Extension slot: CoreBundle can inject payment provider field here */}
                  {additionalFields}

                  {/* Items Table */}
                  <Table
                    dataSource={items}
                    columns={columns}
                    rowKey="orderItemId"
                    pagination={false}
                    loading={loadingItems}
                    className={styles.table}
                    size="small"
                    summary={() => (
                      <Table.Summary fixed>
                        <Table.Summary.Row>
                          <Table.Summary.Cell index={0} colSpan={5} align="right">
                            <strong>{t('coreshop_subtotal', { defaultValue: 'Subtotal' })}:</strong>
                          </Table.Summary.Cell>
                          <Table.Summary.Cell index={1} align="right" />
                          <Table.Summary.Cell index={2} align="right">
                            <strong>{formatCurrency(totals.subtotal, currencyCode)}</strong>
                          </Table.Summary.Cell>
                        </Table.Summary.Row>
                        <Table.Summary.Row>
                          <Table.Summary.Cell index={0} colSpan={5} align="right">
                            <strong>{t('coreshop_tax', { defaultValue: 'Tax' })}:</strong>
                          </Table.Summary.Cell>
                          <Table.Summary.Cell index={1} align="right">
                            <strong>{formatCurrency(totals.tax, currencyCode)}</strong>
                          </Table.Summary.Cell>
                          <Table.Summary.Cell index={2} align="right" />
                        </Table.Summary.Row>
                        <Table.Summary.Row>
                          <Table.Summary.Cell index={0} colSpan={5} align="right">
                            <strong>{t('coreshop_total', { defaultValue: 'Total' })}:</strong>
                          </Table.Summary.Cell>
                          <Table.Summary.Cell index={1} align="right" />
                          <Table.Summary.Cell index={2} align="right">
                            <strong style={{ fontSize: '16px' }}>{formatCurrency(totals.total, currencyCode)}</strong>
                          </Table.Summary.Cell>
                        </Table.Summary.Row>
                      </Table.Summary>
                    )}
                  />
                </div>
              )
            }
          ]}
        />
      </Form>
    </Modal>
  )
}

const useCreateInvoiceModalStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal-body {
      padding: 0;
    }
  `,
  form: css`
    .ant-tabs {
      margin: 0;
    }
    .ant-tabs-nav {
      margin: 0;
      padding: 0 24px;
      background: ${token.colorBgContainer};
    }
  `,
  content: css`
    padding: 24px;
  `,
  table: css`
    margin-top: 16px;

    .ant-table-thead > tr > th {
      background: ${token.colorBgContainer};
      font-weight: 600;
    }
  `
}))
