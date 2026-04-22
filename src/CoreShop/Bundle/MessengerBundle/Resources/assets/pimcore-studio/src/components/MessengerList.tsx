/**
 * MessengerList Component - Main component for messenger management
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
import { Tabs, Button, Space, Badge } from 'antd'
import { ReloadOutlined, WarningOutlined, ClockCircleOutlined } from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import MessengerChart from './MessengerChart'
import MessengerFailedGrid from './MessengerFailedGrid'
import MessengerPendingGrid from './MessengerPendingGrid'
import { useMessengerChart } from '../hooks/useMessenger'

export const MessengerList: React.FC = () => {
  const { data, loading, error, reload } = useMessengerChart()
  const { styles } = useMessengerListStyles()
  const { t } = useTranslation()
  const [activeTab, setActiveTab] = React.useState('pending')
  const [pendingReceiver, setPendingReceiver] = React.useState<string | null>(null)

  const handleGlobalReload = () => {
    reload()
  }

  const handleBarClick = (receiver: string) => {
    setPendingReceiver(receiver)
    setActiveTab('pending')
  }

  const tabItems = [
    {
      key: 'pending',
      label: (
        <Space size={4}>
          <ClockCircleOutlined />
          {t('coreshop_messenger_pending_messages', { defaultValue: 'Pending Messages' })}
        </Space>
      ),
      children: (
        <div className={styles.tabContent}>
          <MessengerPendingGrid selectedReceiver={pendingReceiver} />
        </div>
      ),
    },
    {
      key: 'failed',
      label: (
        <Space size={4}>
          <WarningOutlined />
          {t('coreshop_messenger_failed_messages', { defaultValue: 'Failed Messages' })}
        </Space>
      ),
      children: (
        <div className={styles.tabContent}>
          <MessengerFailedGrid />
        </div>
      ),
    },
  ]

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <div className={styles.title}>
          {t('coreshop_messenger', { defaultValue: 'Messenger' })}
        </div>
        <Button
          type="primary"
          icon={<ReloadOutlined />}
          onClick={handleGlobalReload}
        >
          {t('coreshop_messenger_reload_all', { defaultValue: 'Reload' })}
        </Button>
      </div>

      <div className={styles.chartSection}>
        <MessengerChart data={data} loading={loading} error={error} onBarClick={handleBarClick} />
      </div>

      <div className={styles.tabsSection}>
        <Tabs
          activeKey={activeTab}
          onChange={setActiveTab}
          type="card"
          items={tabItems}
          className={styles.tabs}
        />
      </div>
    </div>
  )
}

const useMessengerListStyles = createStyles(({ css, token }) => ({
  container: css`
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px;
    overflow: hidden;
    background: ${token.colorBgLayout};
  `,
  header: css`
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,
  title: css`
    font-size: 18px;
    font-weight: 600;
    color: ${token.colorText};
  `,
  chartSection: css`
    flex-shrink: 0;
  `,
  tabsSection: css`
    flex: 1;
    min-height: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  `,
  tabs: css`
    height: 100%;
    display: flex;
    flex-direction: column;

    .ant-tabs-nav {
      margin-bottom: 0;
      flex-shrink: 0;

      .ant-tabs-tab {
        padding: 8px 16px;

        &.ant-tabs-tab-active {
          background: ${token.colorBgContainer};
        }
      }
    }

    .ant-tabs-content-holder {
      flex: 1;
      overflow: hidden;
      background: ${token.colorBgContainer};
      border: 1px solid ${token.colorBorderSecondary};
      border-top: none;
      border-radius: 0 0 ${token.borderRadius}px ${token.borderRadius}px;
    }

    .ant-tabs-content {
      height: 100%;
    }

    .ant-tabs-tabpane {
      height: 100%;
      overflow: hidden;
    }
  `,
  tabContent: css`
    height: 100%;
    overflow: hidden;
    padding: 16px;
  `
}))

export default MessengerList