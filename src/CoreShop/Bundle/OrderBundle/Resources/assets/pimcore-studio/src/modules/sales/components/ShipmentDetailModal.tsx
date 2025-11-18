/**
 * CoreShop OrderBundle Shipment Detail Modal
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
import { Modal, Button, Table } from 'antd'
import { createStyles } from 'antd-style'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'

interface ShipmentItem {
  _itemName: string
  quantity: number
}

interface Shipment {
  id: number
  shipmentDate: number
  shipmentNumber: string
  trackingCode?: string
  weight?: number
  carrierName: string
  items: ShipmentItem[]
}

interface ShipmentDetailModalProps {
  open: boolean
  shipment: Shipment
  onClose: () => void
}

/**
 * Shipment Detail Modal
 *
 * Shows shipment details in a read-only modal
 * Pattern from ExtJS: /order/editShipment.js
 */
export const ShipmentDetailModal: React.FC<ShipmentDetailModalProps> = ({
  open,
  shipment,
  onClose
}) => {
  const { styles } = useShipmentDetailModalStyles()
  const { openDataObject } = useDataObjectHelper()

  // Open shipment DataObject
  const handleOpenShipment = () => {
    void openDataObject({ config: { id: shipment.id } })
    onClose()
  }

  return (
    <Modal
      open={open}
      title={null}
      onCancel={onClose}
      footer={[
        <Button key="ok" type="primary" onClick={onClose}>
          OK
        </Button>
      ]}
      width={600}
    >
      <div className={styles.content}>
        <div className={styles.field}>
          <div className={styles.label}>Date:</div>
          <div className={styles.value}>{formatDateTime(shipment.shipmentDate)}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Shipment Number:</div>
          <div className={styles.value}>{shipment.shipmentNumber}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Tracking-Number:</div>
          <div className={styles.value}>{shipment.trackingCode || '-'}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Weight:</div>
          <div className={styles.value}>{shipment.weight || '-'}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Carrier:</div>
          <div className={styles.value}>{shipment.carrierName}</div>
        </div>

        <div className={styles.buttonContainer}>
          <Button
            type="default"
            onClick={handleOpenShipment}
          >
            Open Shipment ({shipment.shipmentNumber})
          </Button>
        </div>

        {/* Products Table */}
        <div className={styles.productsSection}>
          <div className={styles.productsHeader}>Products</div>
          <Table
            dataSource={shipment.items}
            columns={[
              {
                title: 'Item',
                dataIndex: '_itemName',
                key: '_itemName',
                width: '70%'
              },
              {
                title: 'Quantity',
                dataIndex: 'quantity',
                key: 'quantity',
                width: '30%'
              }
            ]}
            pagination={false}
            size="small"
            rowKey={(record, index) => index?.toString() || '0'}
          />
        </div>
      </div>
    </Modal>
  )
}

const useShipmentDetailModalStyles = createStyles(({ css, token }) => ({
  content: css`
    padding: 16px 0;
  `,
  field: css`
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid ${token.colorBorder};
  `,
  label: css`
    width: 180px;
    font-weight: 500;
    color: ${token.colorTextSecondary};
    flex-shrink: 0;
  `,
  value: css`
    flex: 1;
    color: ${token.colorText};
  `,
  buttonContainer: css`
    margin: 16px 0;
    padding: 4px 0 16px 0;
    border-bottom: 1px solid ${token.colorBorder};
  `,
  productsSection: css`
    margin-top: 24px;
  `,
  productsHeader: css`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    color: ${token.colorText};
  `
}))
