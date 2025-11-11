/**
 * CoreShop OrderBundle Header Tab
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
import { createStyles } from 'antd-style'
import type { SaleTabProps } from '../registry'
import type { State } from '../types'

export const HeaderTab: React.FC<SaleTabProps> = ({ sale }) => {
  const { styles } = useHeaderTabStyles()

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
  const formatDate = (date?: string | number) => {
    if (!date) return '-'
    const dateValue = typeof date === 'number' ? date * 1000 : date
    return new Date(dateValue).toLocaleString('de-DE')
  }

  // Render state info with color dot
  const renderStateInfo = (label: string, state?: string | State) => {
    const stateLabel = typeof state === 'object' ? state.label || state.state : state || 'N/A'
    const stateColor = typeof state === 'object' ? state.color : '#999'

    return (
      <div className={styles.stateInfo}>
        <div className={styles.stateLabel}>{label}</div>
        <div className={styles.stateValue}>
          <span className={styles.colorDot} style={{ backgroundColor: stateColor }} />
          {stateLabel}
        </div>
      </div>
    )
  }

  const storeName = typeof sale.store === 'object' ? sale.store?.name : sale.store || '-'

  // Get state info from correct field names (orderPaymentState, orderShippingState, orderInvoiceState)
  const orderStateInfo = (sale as any).orderState
  const paymentStateInfo = (sale as any).orderPaymentState || sale.paymentState
  const shippingStateInfo = (sale as any).orderShippingState || sale.shipmentState
  const invoiceStateInfo = (sale as any).orderInvoiceState || sale.invoiceState

  return (
    <div className={styles.container}>
      {/* First Row: States - Always shown */}
      <div className={styles.row}>
        <div className={styles.cell}>
          {renderStateInfo('State', orderStateInfo)}
        </div>
        <div className={styles.cell}>
          {renderStateInfo('Payment State', paymentStateInfo)}
        </div>
        <div className={styles.cell}>
          {renderStateInfo('Shipping State', shippingStateInfo)}
        </div>
        <div className={styles.cell}>
          {renderStateInfo('Invoice State', invoiceStateInfo)}
        </div>
      </div>

      {/* Second Row: Info */}
      <div className={styles.row}>
        <div className={styles.cell}>
          <div className={styles.infoLabel}>Date</div>
          <div className={styles.infoValueBig}>{formatDate(sale.saleDate)}</div>
        </div>
        <div className={styles.cell}>
          <div className={styles.infoLabel}>Total</div>
          <div className={styles.infoValueBig}>{formatCurrency(sale.totalGross)}</div>
        </div>
        <div className={styles.cell}>
          <div className={styles.infoLabel}>Product(s)</div>
          <div className={styles.infoValueBig}>{sale.items?.length || 0}</div>
        </div>
        <div className={styles.cell}>
          <div className={styles.infoLabel}>Store</div>
          <div className={styles.infoValueBig}>{storeName}</div>
        </div>
      </div>
    </div>
  )
}

const useHeaderTabStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    gap: 0;
  `,
  row: css`
    display: flex;
    gap: 0;
  `,
  cell: css`
    flex: 1;
    padding: 20px;
    background: ${token.colorBgContainer};
    border: 1px solid ${token.colorBorder};
    border-right-width: 0;

    &:last-child {
      border-right-width: 1px;
    }
  `,
  stateInfo: css`
    display: flex;
    flex-direction: column;
    gap: 4px;
  `,
  stateLabel: css`
    font-size: 12px;
    color: ${token.colorTextSecondary};
  `,
  stateValue: css`
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 500;
  `,
  colorDot: css`
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
  `,
  infoLabel: css`
    font-size: 12px;
    color: ${token.colorTextSecondary};
    margin-bottom: 4px;
  `,
  infoValueBig: css`
    font-size: 18px;
    font-weight: 600;
    color: ${token.colorText};
  `
}))
