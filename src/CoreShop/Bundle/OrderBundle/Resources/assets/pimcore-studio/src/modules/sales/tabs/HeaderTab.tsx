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
import { formatDateTime, formatCurrency, getCurrencyCode } from '@coreshop/pimcore/src/utils'
import type { SaleTabProps } from '../registry'
import type { State } from '../types'
import { useSaleContext } from '../context/SaleActionsContext'

export const HeaderTab: React.FC<SaleTabProps> = () => {
  const { sale } = useSaleContext()
  const { styles } = useHeaderTabStyles()

  if (!sale) return null

  const currencyCode = getCurrencyCode(sale.currency)

  // Render state pill badge
  const renderStateBadge = (label: string, state?: string | State) => {
    const stateLabel = typeof state === 'object' ? state.label || state.state : state || 'N/A'
    const stateColor = typeof state === 'object' ? state.color : '#999'

    return (
      <div className={styles.stateCard}>
        <div className={styles.stateLabel}>{label}</div>
        <span
          className={styles.statePill}
          style={{ backgroundColor: stateColor }}
        >
          {stateLabel}
        </span>
      </div>
    )
  }

  const storeName = typeof sale.store === 'object' ? sale.store?.name : sale.store || '-'

  const orderStateInfo = (sale as any).orderState
  const paymentStateInfo = (sale as any).orderPaymentState || sale.paymentState
  const shippingStateInfo = (sale as any).orderShippingState || sale.shipmentState
  const invoiceStateInfo = (sale as any).orderInvoiceState || sale.invoiceState

  return (
    <div className={styles.container}>
      {/* States Row */}
      <div className={styles.statesRow}>
        {renderStateBadge('Order', orderStateInfo)}
        {renderStateBadge('Payment', paymentStateInfo)}
        {renderStateBadge('Shipping', shippingStateInfo)}
        {renderStateBadge('Invoice', invoiceStateInfo)}
      </div>

      {/* Key Metrics Row */}
      <div className={styles.metricsRow}>
        <div className={styles.metricCard}>
          <div className={styles.metricLabel}>Date</div>
          <div className={styles.metricValue}>{formatDateTime(sale.saleDate)}</div>
        </div>
        <div className={styles.metricCardHighlight}>
          <div className={styles.metricLabel}>Total</div>
          <div className={styles.metricValueLarge}>{formatCurrency(sale.totalGross, currencyCode)}</div>
        </div>
        <div className={styles.metricCard}>
          <div className={styles.metricLabel}>Products</div>
          <div className={styles.metricValue}>{sale.items?.length || 0}</div>
        </div>
        <div className={styles.metricCard}>
          <div className={styles.metricLabel}>Store</div>
          <div className={styles.metricValue}>{storeName}</div>
        </div>
      </div>
    </div>
  )
}

const useHeaderTabStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    gap: 12px;
  `,
  statesRow: css`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,
  stateCard: css`
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 12px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  stateLabel: css`
    font-size: 11px;
    font-weight: 500;
    color: ${token.colorTextTertiary};
    text-transform: uppercase;
    letter-spacing: 0.05em;
  `,
  statePill: css`
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    line-height: 1.4;
    white-space: nowrap;
  `,
  metricsRow: css`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,
  metricCard: css`
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 16px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  metricCardHighlight: css`
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 16px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorPrimary}40;
    box-shadow: 0 0 0 1px ${token.colorPrimary}10;
  `,
  metricLabel: css`
    font-size: 11px;
    font-weight: 500;
    color: ${token.colorTextTertiary};
    text-transform: uppercase;
    letter-spacing: 0.05em;
  `,
  metricValue: css`
    font-size: 16px;
    font-weight: 600;
    color: ${token.colorText};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `,
  metricValueLarge: css`
    font-size: 20px;
    font-weight: 700;
    color: ${token.colorText};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `
}))
