/**
 * MessengerChart Component - Vertical bar chart showing message counts per queue
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import * as React from 'react'
import { Spin, Alert, Tooltip, Typography } from 'antd'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import { MessengerChartData } from '../types'

const { Text } = Typography

export interface MessengerChartProps {
  data: MessengerChartData[]
  loading: boolean
  error: string | null
  onBarClick?: (receiver: string) => void
}

export const MessengerChart: React.FC<MessengerChartProps> = ({ data, loading, error, onBarClick }) => {
  const { styles, theme } = useMessengerChartStyles()
  const { t } = useTranslation()

  if (error) {
    return (
      <Alert
        message={t('coreshop_messenger_error_loading', { defaultValue: 'Error loading chart data' })}
        description={error}
        type="error"
      />
    )
  }

  if (loading) {
    return (
      <div className={styles.loadingContainer}>
        <Spin size="large" />
      </div>
    )
  }

  const maxCount = Math.max(...data.map(item => item.count), 1)
  const totalMessages = data.reduce((sum, item) => sum + item.count, 0)

  // Generate colors for bars
  const getBarColor = (index: number): string => {
    const colors = [
      theme.colorPrimary,
      theme.colorSuccess,
      theme.colorWarning,
      theme.colorInfo,
      '#722ed1', // purple
      '#13c2c2', // cyan
      '#eb2f96', // magenta
      '#fa8c16', // orange
    ]
    return colors[index % colors.length]
  }

  return (
    <div className={styles.container}>
      {/* Summary */}
      <div className={styles.summary}>
        <div className={styles.summaryItem}>
          <span className={styles.summaryValue}>{totalMessages}</span>
          <span className={styles.summaryLabel}>{t('coreshop_messenger_total', { defaultValue: 'Total' })}</span>
        </div>
        <div className={styles.summaryDivider} />
        <div className={styles.summaryItem}>
          <span className={styles.summaryValue}>{data.length}</span>
          <span className={styles.summaryLabel}>{t('coreshop_messenger_queues', { defaultValue: 'Queues' })}</span>
        </div>
      </div>

      {/* Bar Chart */}
      <div className={styles.chartArea}>
        {data.length === 0 ? (
          <div className={styles.emptyState}>
            <Text type="secondary">{t('coreshop_messenger_no_data', { defaultValue: 'No pending messages' })}</Text>
          </div>
        ) : (
          <div className={styles.barsContainer}>
            {data.map((item, index) => {
              const heightPercent = Math.max((item.count / maxCount) * 100, item.count > 0 ? 15 : 0)
              const color = getBarColor(index)

              return (
                <Tooltip
                  key={index}
                  title={`${item.receiver} (${item.count})`}
                  placement="top"
                >
                  <div className={styles.barWrapper} onClick={() => onBarClick?.(item.receiver)}>
                    <div className={styles.barOuter}>
                      <div
                        className={styles.bar}
                        style={{
                          height: `${heightPercent}%`,
                          backgroundColor: color
                        }}
                      >
                        {item.count > 0 && (
                          <span className={styles.barValue}>{item.count}</span>
                        )}
                      </div>
                    </div>
                    <div className={styles.barLabel}>{item.receiver}</div>
                  </div>
                </Tooltip>
              )
            })}
          </div>
        )}
      </div>
    </div>
  )
}

const useMessengerChartStyles = createStyles(({ css, token }) => ({
  container: css`
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadius}px;
    border: 1px solid ${token.colorBorderSecondary};
    padding: 16px;
    margin-bottom: 16px;
  `,
  loadingContainer: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 180px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadius}px;
    border: 1px solid ${token.colorBorderSecondary};
    margin-bottom: 16px;
  `,
  summary: css`
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
  `,
  summaryItem: css`
    display: flex;
    align-items: baseline;
    gap: 6px;
  `,
  summaryValue: css`
    font-size: 20px;
    font-weight: 600;
    color: ${token.colorText};
  `,
  summaryLabel: css`
    font-size: 12px;
    color: ${token.colorTextSecondary};
  `,
  summaryDivider: css`
    width: 1px;
    height: 24px;
    background: ${token.colorBorderSecondary};
  `,
  chartArea: css`
    height: 140px;
    overflow-x: auto;
    overflow-y: hidden;
  `,
  emptyState: css`
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  `,
  barsContainer: css`
    display: flex;
    align-items: flex-end;
    height: 100%;
    gap: 6px;
    padding: 0 4px;
  `,
  barWrapper: css`
    flex: 1 1 0;
    min-width: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    cursor: pointer;

    &:hover .bar {
      opacity: 0.85;
      transform: scaleX(1.05);
    }
  `,
  barOuter: css`
    flex: 1;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
  `,
  bar: css`
    width: 100%;
    min-height: 0;
    border-radius: ${token.borderRadiusSM}px ${token.borderRadiusSM}px 0 0;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 4px;
    transition: all 0.2s ease;
  `,
  barValue: css`
    font-size: 11px;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
  `,
  barLabel: css`
    font-size: 10px;
    color: ${token.colorTextSecondary};
    text-align: center;
    margin-top: 6px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  `
}))

export default MessengerChart