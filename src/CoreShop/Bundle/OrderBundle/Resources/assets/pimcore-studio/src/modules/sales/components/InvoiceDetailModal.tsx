/**
 * CoreShop OrderBundle Invoice Detail Modal
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
import { formatDateTime, formatCurrency } from '@coreshop/pimcore/src/utils'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'

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
  items: InvoiceItem[]
}

interface InvoiceDetailModalProps {
  open: boolean
  invoice: Invoice
  currencyCode: string
  onClose: () => void
}

/**
 * Invoice Detail Modal
 *
 * Shows invoice details in a read-only modal
 * Pattern from ExtJS: /order/editInvoice.js
 */
export const InvoiceDetailModal: React.FC<InvoiceDetailModalProps> = ({
  open,
  invoice,
  currencyCode,
  onClose
}) => {
  const { styles } = useInvoiceDetailModalStyles()
  const { openDataObject } = useDataObjectHelper()

  // Open invoice DataObject
  const handleOpenInvoice = () => {
    void openDataObject({ config: { id: invoice.id } })
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
          <div className={styles.value}>{formatDateTime(invoice.invoiceDate)}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Invoice Number:</div>
          <div className={styles.value}>{invoice.invoiceNumber}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Total (excl.):</div>
          <div className={styles.value}>{formatCurrency(invoice.totalNet, currencyCode)}</div>
        </div>

        <div className={styles.field}>
          <div className={styles.label}>Total:</div>
          <div className={styles.value}>{formatCurrency(invoice.totalGross, currencyCode)}</div>
        </div>

        <div className={styles.buttonContainer}>
          <Button
            type="default"
            onClick={handleOpenInvoice}
          >
            Open Invoice ({invoice.invoiceNumber})
          </Button>
        </div>

        {/* Details Table */}
        <div className={styles.detailsSection}>
          <div className={styles.detailsHeader}>Details</div>
          <Table
            dataSource={invoice.items}
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

const useInvoiceDetailModalStyles = createStyles(({ css, token }) => ({
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
  detailsSection: css`
    margin-top: 24px;
  `,
  detailsHeader: css`
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    color: ${token.colorText};
  `
}))
