/**
 * CoreShop OrderBundle Create Shipment Modal (Base Version)
 *
 * This is the base version without carrier selection.
 * CoreBundle extends this by registering an enhanced version with CarrierSelect.
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
import { Modal, Form, Input, InputNumber, Table } from 'antd'
import { createStyles } from 'antd-style'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { formatCurrency } from '@coreshop/pimcore/src/utils'
import { container } from '@pimcore/studio-ui-bundle'
import type { ColumnType } from 'antd/es/table'
import { ModalFieldExtensionRegistry } from '../extensions'
import { extensionServiceIds } from '../extensions/service-ids'
import { getErrorMessage } from '@coreshop/resource/src/entities'

interface ShipmentItem {
  orderItemId: number
  name: string
  price: number
  quantity: number
  quantityShipped: number
  maxToShip: number
  toShip: number
}

export interface CreateShipmentModalProps {
  open: boolean
  orderId: number
  currencyCode: string
  carrierId?: number
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Create Shipment Modal (Base Version)
 *
 * Pattern from ExtJS: /order/shipment.js (OrderBundle)
 * Note: CoreBundle extends this by adding carrier selection
 */
export const CreateShipmentModal: React.FC<CreateShipmentModalProps> = ({
  open,
  orderId,
  currencyCode,
  carrierId,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useCreateShipmentModalStyles()
  const [form] = Form.useForm()
  const [items, setItems] = React.useState<ShipmentItem[]>([])
  const [loading, setLoading] = React.useState(false)
  const [loadingItems, setLoadingItems] = React.useState(false)

  // Get additional fields from extension registry
  const extensionRegistry = React.useMemo(
    () => container.get<ModalFieldExtensionRegistry>(extensionServiceIds.modalFieldExtensionRegistry),
    []
  )
  const additionalFields = React.useMemo(
    () => extensionRegistry.getFields('create-shipment', { form, orderId, currencyCode, carrierId }),
    [extensionRegistry, form, orderId, currencyCode, carrierId]
  )

  // Load processable items
  React.useEffect(() => {
    if (!open) return

    const loadItems = async () => {
      setLoadingItems(true)
      try {
        const response = await fetch(`/pimcore-studio/api/coreshop/order-shipment/get-ship-able-items?id=${orderId}`)
        const data = await response.json()

        if (data.success && data.items && data.items.length > 0) {
          setItems(data.items)
        } else {
          void messageApi.warning(t('coreshop_shipment_no_items', { defaultValue: 'No shippable items found' }))
          onCancel()
        }
      } catch (error) {
        void messageApi.error(getErrorMessage(error, t('coreshop_shipment_load_items_error', { defaultValue: 'Failed to load items' })))
        onCancel()
      } finally {
        setLoadingItems(false)
      }
    }

    void loadItems()
  }, [open, orderId, onCancel, t])

  // Handle quantity change
  const handleQuantityChange = (orderItemId: number, value: number | null) => {
    setItems(items.map(item =>
      item.orderItemId === orderItemId
        ? { ...item, toShip: Math.min(Math.max(value || 0, 0), item.maxToShip) }
        : item
    ))
  }

  // Handle save
  const handleSave = async () => {
    try {
      const values = await form.validateFields()
      const itemsToShip = items
        .filter(item => item.toShip > 0)
        .map(item => ({
          orderItemId: item.orderItemId,
          quantity: item.toShip
        }))

      if (itemsToShip.length === 0) {
        void messageApi.warning(t('coreshop_shipment_select_items', { defaultValue: 'Please select items to ship' }))
        return
      }

      setLoading(true)

      const payload = {
        ...values,
        id: orderId,
        items: itemsToShip
      }

      const response = await fetch('/pimcore-studio/api/coreshop/order-shipment/create-shipment', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      const data = await response.json()

      if (data.success) {
        void messageApi.success(t('coreshop_shipment_create_success', { defaultValue: 'Shipment created successfully' }))
        onSuccess()
      } else {
        void messageApi.error(data.message || t('coreshop_shipment_create_error', { defaultValue: 'Failed to create shipment' }))
      }
    } catch (error) {
      void messageApi.error(getErrorMessage(error, t('coreshop_shipment_create_error', { defaultValue: 'Failed to create shipment' })))
    } finally {
      setLoading(false)
    }
  }

  const columns: Array<ColumnType<ShipmentItem>> = [
    {
      title: t('coreshop_product', { defaultValue: 'Product' }),
      dataIndex: 'name',
      key: 'name',
      width: '30%'
    },
    {
      title: t('coreshop_price', { defaultValue: 'Price' }),
      dataIndex: 'price',
      key: 'price',
      width: '15%',
      align: 'right',
      render: (price) => formatCurrency(price, currencyCode)
    },
    {
      title: t('coreshop_quantity', { defaultValue: 'Quantity' }),
      dataIndex: 'quantity',
      key: 'quantity',
      width: '12%',
      align: 'right'
    },
    {
      title: t('coreshop_shipped_quantity', { defaultValue: 'Shipped Quantity' }),
      dataIndex: 'quantityShipped',
      key: 'quantityShipped',
      width: '15%',
      align: 'right'
    },
    {
      title: t('coreshop_to_ship', { defaultValue: 'To Ship' }),
      dataIndex: 'toShip',
      key: 'toShip',
      width: '18%',
      align: 'right',
      render: (value, record) => (
        <InputNumber
          value={value}
          min={0}
          max={record.maxToShip}
          onChange={(val) => handleQuantityChange(record.orderItemId, val)}
          style={{ width: '100%' }}
        />
      )
    }
  ]

  return (
    <Modal
      open={open}
      title={`${t('coreshop_shipment_create_new', { defaultValue: 'Create Shipment for Order' })} (${orderId})`}
      onCancel={onCancel}
      onOk={handleSave}
      okText={t('coreshop_save', { defaultValue: 'Save' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      width={900}
      confirmLoading={loading}
    >
      <Form
        form={form}
        layout="vertical"
        initialValues={{
          carrier: carrierId,
          trackingCode: ''
        }}
      >
        <div className={styles.section}>
          <div className={styles.sectionHeader}>{t('coreshop_shipment', { defaultValue: 'Shipment' })}</div>

          {/* Extension slot: CoreBundle injects carrier field here */}
          {additionalFields}

          <Form.Item
            label={t('coreshop_tracking_code', { defaultValue: 'Tracking Number' })}
            name="trackingCode"
          >
            <Input placeholder={t('coreshop_tracking_code', { defaultValue: 'Tracking Number' })} />
          </Form.Item>
        </div>

        <Table
          dataSource={items}
          columns={columns}
          rowKey="orderItemId"
          pagination={false}
          loading={loadingItems}
          size="small"
          className={styles.table}
        />
      </Form>
    </Modal>
  )
}

const useCreateShipmentModalStyles = createStyles(({ css, token }) => ({
  section: css`
    margin-bottom: 24px;
  `,
  sectionHeader: css`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: ${token.colorText};
    display: flex;
    align-items: center;
    gap: 8px;

    &:before {
      content: '';
      display: inline-block;
      width: 20px;
      height: 20px;
      background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2352c41a"><path d="M3 13.5L2.25 12H7.5L6 9.5H17L15 12H22L20.5 10V19H22V21H2V19H3.5V13.5H3ZM5.5 19H18.5V12.5H14.5L16.5 10H7.5L9 12.5H5.5V19Z"/></svg>') no-repeat center;
      background-size: contain;
    }
  `,
  table: css`
    .ant-table-thead > tr > th {
      background: ${token.colorBgContainer};
      font-weight: 600;
    }
  `
}))
