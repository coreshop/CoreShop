/**
 * CoreShop OrderBundle Customer Tab
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
import { Card, Tabs, Button } from 'antd'
import { createStyles } from 'antd-style'
import { FolderOpenOutlined } from '@ant-design/icons'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'
import type { SaleTabProps } from '../registry'

interface Address {
  id?: number
  formatted?: string
}

interface Customer {
  id?: number
  firstname?: string
  lastname?: string
  email?: string
  creationDate?: number
}

export const CustomerTab: React.FC<SaleTabProps> = ({ sale }) => {
  const { styles } = useCustomerTabStyles()
  const { openDataObject } = useDataObjectHelper()

  const customer = (sale as any).customer as Customer | undefined
  const shippingAddress = (sale as any).address?.shipping as Address | undefined
  const invoiceAddress = (sale as any).address?.billing as Address | undefined

  // Format date
  const formatDate = (date?: string | number) => {
    if (!date) return '-'
    const dateValue = typeof date === 'number' ? date * 1000 : date
    return new Date(dateValue).toLocaleString('de-DE')
  }

  // Open customer DataObject
  const handleOpenCustomer = () => {
    if (customer?.id) {
      void openDataObject({ config: { id: customer.id } })
    }
  }

  // Open address DataObject
  const handleOpenAddress = (addressId?: number) => {
    if (addressId) {
      void openDataObject({ config: { id: addressId } })
    }
  }

  // Render address content
  const renderAddress = (address?: Address) => {
    if (!address) {
      return <div className={styles.noData}>No address available</div>
    }

    // Parse HTML and convert <br> to line breaks
    const formatAddress = (html: string) => {
      return html.replace(/<br\s*\/?>/gi, '').replace(/<[^>]*>/g, '')
    }

    return (
      <div className={styles.addressContent}>
        <div className={styles.addressText}>
          {address.formatted ? formatAddress(address.formatted) : 'No address data'}
        </div>
        <div className={styles.openButtonContainer}>
          <Button
            type="default"
            onClick={() => handleOpenAddress(address?.id)}
            className={styles.openButton}
          >
            Open
          </Button>
        </div>
      </div>
    )
  }

  const tabItems = [
    {
      key: 'shipping',
      label: 'Shipping Address',
      children: renderAddress(shippingAddress)
    },
    {
      key: 'invoice',
      label: 'Invoice Address',
      children: renderAddress(invoiceAddress)
    }
  ]

  return (
    <Card
      title={`Customer: ${customer?.firstname || ''} ${customer?.lastname || ''} (${customer?.id || 'N/A'})`}
      className={styles.card}
      extra={
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          onClick={handleOpenCustomer}
          title="Open Customer DataObject"
        />
      }
    >
      <div className={styles.container}>
        {/* Customer Info */}
        <div className={styles.infoSection}>
          <div className={styles.infoItem}>
            <strong>Email</strong>
            <div>{customer?.email || '-'}</div>
          </div>
          <div className={styles.infoItem}>
            <strong>Customer created at</strong>
            <div>{formatDate(customer?.creationDate)}</div>
          </div>
        </div>

        {/* Address Tabs */}
        <Tabs items={tabItems} className={styles.tabs} />
      </div>
    </Card>
  )
}

const useCustomerTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }
  `,
  container: css`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,
  infoSection: css`
    display: flex;
    flex-direction: column;
    padding: 16px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
  `,
  infoItem: css`
    display: flex;
    flex-direction: column;
    gap: 4px;

    strong {
      font-weight: 500;
      color: ${token.colorTextSecondary};
    }
  `,
  tabs: css`
    .ant-tabs-nav {
      margin-bottom: 0;
      padding: 0 16px;
    }

    .ant-tabs-content {
      padding: 0;
    }
  `,
  addressContent: css`
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 16px;
  `,
  addressText: css`
    flex: 1;
    white-space: pre-line;
    line-height: 1.6;
  `,
  openButtonContainer: css`
    flex-shrink: 0;
  `,
  openButton: css`
    /* Button styling */
  `,
  noData: css`
    padding: 16px;
    color: ${token.colorTextTertiary};
  `
}))
