/**
 * MessengerChart Component
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
import { Row, Col, Spin, Alert, Statistic, Progress, Empty, Tooltip as AntTooltip } from 'antd'
import { MessageOutlined, ClockCircleOutlined } from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import { useMessengerChart } from '../hooks/useMessenger'

export const MessengerChart: React.FC = () => {
  const { data, loading, error } = useMessengerChart()
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

  const totalMessages = data.reduce((sum, item) => sum + item.count, 0)
  const maxCount = Math.max(...data.map(item => item.count), 1)

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
    ]
    return colors[index % colors.length]
  }

  return (
    <div className={styles.container}>
      <Row gutter={16}>
        <Col span={6}>
          <div className={styles.statisticCard}>
            <Statistic
              title={t('coreshop_messenger_total_messages', { defaultValue: 'Total Messages' })}
              value={totalMessages}
              prefix={<MessageOutlined />}
              valueStyle={{ color: theme.colorPrimary }}
            />
          </div>
        </Col>
        <Col span={6}>
          <div className={styles.statisticCard}>
            <Statistic
              title={t('coreshop_messenger_receivers', { defaultValue: 'Receivers' })}
              value={data.length}
              prefix={<ClockCircleOutlined />}
              valueStyle={{ color: theme.colorSuccess }}
            />
          </div>
        </Col>
        <Col span={12}>
          <div className={styles.chartCard}>
            {data.length === 0 ? (
              <Empty
                image={Empty.PRESENTED_IMAGE_SIMPLE}
                description={t('coreshop_messenger_no_data', { defaultValue: 'No pending messages' })}
              />
            ) : (
              <div className={styles.chartContainer}>
                {data.map((item, index) => {
                  const percentage = Math.round((item.count / maxCount) * 100)
                  const color = getBarColor(index)
                  const shortName = item.receiver.split('\\').pop() || item.receiver

                  return (
                    <AntTooltip
                      key={index}
                      title={`${item.receiver}: ${item.count}`}
                      placement="top"
                    >
                      <div className={styles.barItem}>
                        <div className={styles.barLabel}>{shortName}</div>
                        <Progress
                          percent={percentage}
                          showInfo={false}
                          strokeColor={color}
                          trailColor={theme.colorBgLayout}
                          size="small"
                        />
                        <div className={styles.barCount}>{item.count}</div>
                      </div>
                    </AntTooltip>
                  )
                })}
              </div>
            )}
          </div>
        </Col>
      </Row>
    </div>
  )
}

const useMessengerChartStyles = createStyles(({ css, token }) => ({
  container: css`
    margin-bottom: 16px;
  `,
  loadingContainer: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 120px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadius}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  statisticCard: css`
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadius}px;
    padding: 16px;
    border: 1px solid ${token.colorBorderSecondary};
    height: 100%;

    .ant-statistic-title {
      color: ${token.colorTextSecondary};
      font-size: 13px;
    }
  `,
  chartCard: css`
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadius}px;
    padding: 12px 16px;
    border: 1px solid ${token.colorBorderSecondary};
    height: 100%;
    min-height: 88px;
  `,
  chartContainer: css`
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-height: 100px;
    overflow-y: auto;
  `,
  barItem: css`
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;

    &:hover {
      background: ${token.colorBgLayout};
      margin: -2px -4px;
      padding: 2px 4px;
      border-radius: ${token.borderRadiusSM}px;
    }
  `,
  barLabel: css`
    width: 120px;
    font-size: 12px;
    color: ${token.colorTextSecondary};
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
  `,
  barCount: css`
    min-width: 32px;
    text-align: right;
    font-size: 12px;
    font-weight: 600;
    color: ${token.colorText};
  `
}))

export default MessengerChart