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
import { FolderOpenOutlined, UserOutlined, EnvironmentOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'
import type { SaleTabProps } from '../registry'
import { useSaleContext } from '../context/SaleActionsContext'

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

export const CustomerTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const { sale } = useSaleContext()
  const { styles } = useCustomerTabStyles()
  const { openDataObject } = useDataObjectHelper()

  if (!sale) return null

  const customer = (sale as any).customer as Customer | undefined
  const shippingAddress = (sale as any).address?.shipping as Address | undefined
  const invoiceAddress = (sale as any).address?.billing as Address | undefined

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
      return html.replace(/<br\s*\/?>/gi, '\n').replace(/<[^>]*>/g, '').replace(/^\n+|\n+$/g, '')
    }

    return (
      <div className={styles.addressContent}>
        <EnvironmentOutlined className={styles.addressIcon} />
        <div className={styles.addressText}>
          {address.formatted ? formatAddress(address.formatted) : 'No address data'}
        </div>
        {address.id && (
          <Button
            type="text"
            size="small"
            icon={<FolderOpenOutlined />}
            onClick={() => handleOpenAddress(address?.id)}
            title="Open Address"
          />
        )}
      </div>
    )
  }

  const initials = [customer?.firstname?.[0], customer?.lastname?.[0]].filter(Boolean).join('').toUpperCase() || '?'

  const tabItems = [
    {
      key: 'shipping',
      label: t('coreshop_shipping_address', { defaultValue: 'Shipping Address' }),
      children: renderAddress(shippingAddress)
    },
    {
      key: 'invoice',
      label: t('coreshop_invoice_address', { defaultValue: 'Invoice Address' }),
      children: renderAddress(invoiceAddress)
    }
  ]

  return (
    <Card
      title={t('coreshop_customer', { defaultValue: 'Customer' })}
      className={styles.card}
      extra={
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          onClick={handleOpenCustomer}
          size="small"
          title={t('coreshop_open_customer_data_object', { defaultValue: 'Open Customer DataObject' })}
        />
      }
    >
      <div className={styles.container}>
        {/* Customer Identity */}
        <div className={styles.customerHeader}>
          <div className={styles.avatar}>
            {initials.length > 0 ? initials : <UserOutlined />}
          </div>
          <div className={styles.customerInfo}>
            <div className={styles.customerName}>
              {customer?.firstname || ''} {customer?.lastname || ''}
            </div>
            <div className={styles.customerEmail}>{customer?.email || '-'}</div>
            <div className={styles.customerSince}>
              {t('coreshop_customer_since', { defaultValue: 'Customer since' })} {formatDateTime(customer?.creationDate)}
            </div>
          </div>
        </div>

        {/* Address Tabs */}
        <Tabs items={tabItems} className={styles.tabs} size="small" />
      </div>
    </Card>
  )
}

const useCustomerTabStyles = createStyles(({ css, token }) => ({
  card: css``,
  container: css`
    display: flex;
    flex-direction: column;
  `,
  customerHeader: css`
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 0 0 8px 0;
  `,
  avatar: css`
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: ${token.colorPrimary}15;
    color: ${token.colorPrimary};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    flex-shrink: 0;
  `,
  customerInfo: css`
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  `,
  customerName: css`
    font-size: 14px;
    font-weight: 600;
    color: ${token.colorText};
  `,
  customerEmail: css`
    font-size: 13px;
    color: ${token.colorTextSecondary};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,
  customerSince: css`
    font-size: 11px;
    color: ${token.colorTextTertiary};
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
    gap: 10px;
    padding: 14px 16px;
  `,
  addressIcon: css`
    color: ${token.colorTextTertiary};
    font-size: 16px;
    margin-top: 2px;
    flex-shrink: 0;
  `,
  addressText: css`
    flex: 1;
    white-space: pre-line;
    line-height: 1;
    color: ${token.colorText};
  `,
  noData: css`
    padding: 16px;
    color: ${token.colorTextTertiary};
    font-size: 13px;
  `
}))
