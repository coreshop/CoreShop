/**
 * MessengerPendingGrid Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect } from 'react'
import {
  Table,
  Select,
  Button,
  Space,
  Modal,
  Alert,
  Typography,
  Tooltip,
  Tag,
  Empty
} from 'antd'
import {
  InfoCircleOutlined,
  ReloadOutlined
} from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import { ColumnsType } from 'antd/es/table'
import { useMessengerReceivers, useMessengerMessages } from '../hooks/useMessenger'
import { MessengerMessage } from '../types'

const { Paragraph } = Typography

export interface MessengerPendingGridProps {
  selectedReceiver?: string | null
}

export const MessengerPendingGrid: React.FC<MessengerPendingGridProps> = ({ selectedReceiver: externalReceiver }) => {
  const { receivers, loading: receiversLoading } = useMessengerReceivers()
  const [selectedReceiver, setSelectedReceiver] = useState<string | null>(externalReceiver ?? null)
  const [infoModalOpen, setInfoModalOpen] = useState(false)
  const [selectedMessage, setSelectedMessage] = useState<MessengerMessage | null>(null)
  const { styles } = usePendingGridStyles()
  const { t } = useTranslation()

  const {
    messages,
    loading: messagesLoading,
    error,
    reload
  } = useMessengerMessages(selectedReceiver)

  useEffect(() => {
    if (externalReceiver != null) {
      setSelectedReceiver(externalReceiver)
    }
  }, [externalReceiver])

  const handleReceiverChange = (value: string) => {
    setSelectedReceiver(value)
  }

  const handleInfoClick = (record: MessengerMessage) => {
    setSelectedMessage(record)
    setInfoModalOpen(true)
  }

  // Extract short class name from full namespace
  const getShortClassName = (fullClass: string): string => {
    return fullClass.split('\\').pop() || fullClass
  }

  const columns: ColumnsType<MessengerMessage> = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 100,
      render: (id: string) => (
        <Tag>{id}</Tag>
      )
    },
    {
      title: t('coreshop_messenger_class', { defaultValue: 'Class' }),
      dataIndex: 'class',
      key: 'class',
      ellipsis: true,
      render: (className: string) => (
        <Tooltip title={className}>
          <span className={styles.className}>{getShortClassName(className)}</span>
        </Tooltip>
      )
    },
    {
      title: t('coreshop_messenger_actions', { defaultValue: 'Actions' }),
      key: 'actions',
      width: 80,
      fixed: 'right',
      render: (_, record) => (
        <Space size="small">
          <Tooltip title={t('coreshop_messenger_info', { defaultValue: 'Details' })}>
            <Button
              size="small"
              icon={<InfoCircleOutlined />}
              onClick={() => handleInfoClick(record)}
            />
          </Tooltip>
        </Space>
      ),
    },
  ]

  return (
    <div className={styles.container}>
      <div className={styles.toolbar}>
        <Select
          className={styles.receiverSelect}
          placeholder={t('coreshop_messenger_receivers', { defaultValue: 'Select receiver' })}
          value={selectedReceiver}
          onChange={handleReceiverChange}
          loading={receiversLoading}
          allowClear
          showSearch
          filterOption={(input, option) =>
            (option?.children as unknown as string)?.toLowerCase().includes(input.toLowerCase())
          }
        >
          {receivers.map(receiver => (
            <Select.Option key={receiver.receiver} value={receiver.receiver}>
              {receiver.receiver}
            </Select.Option>
          ))}
        </Select>

        <Button
          icon={<ReloadOutlined />}
          onClick={reload}
          disabled={!selectedReceiver}
        >
          {t('coreshop_messenger_reload', { defaultValue: 'Reload' })}
        </Button>
      </div>

      {error && (
        <Alert
          message={t('coreshop_messenger_error_loading', { defaultValue: 'Error loading messages' })}
          description={error}
          type="error"
          className={styles.errorAlert}
          closable
        />
      )}

      {!selectedReceiver ? (
        <Empty
          className={styles.emptyState}
          description={t('coreshop_messenger_select_receiver_pending', { defaultValue: 'Please select a receiver to view pending messages' })}
        />
      ) : (
        <Table
          columns={columns}
          dataSource={messages}
          rowKey="id"
          loading={messagesLoading}
          scroll={{ y: 'calc(100vh - 520px)' }}
          pagination={false}
          size="small"
          className={styles.table}
        />
      )}

      {/* Info Modal */}
      <Modal
        title={t('coreshop_messenger_message_info', { defaultValue: 'Message Information' })}
        open={infoModalOpen}
        onCancel={() => setInfoModalOpen(false)}
        footer={[
          <Button key="close" onClick={() => setInfoModalOpen(false)}>
            {t('coreshop_messenger_close', { defaultValue: 'Close' })}
          </Button>,
        ]}
        width={700}
      >
        {selectedMessage && (
          <div className={styles.modalContent}>
            <div className={styles.modalMeta}>
              <Tag>ID: {selectedMessage.id}</Tag>
              <Tag color="blue">{getShortClassName(selectedMessage.class)}</Tag>
            </div>
            <Paragraph className={styles.codeBlock}>
              <pre>
                {selectedMessage.serialized || t('coreshop_messenger_no_data', { defaultValue: 'No data available' })}
              </pre>
            </Paragraph>
          </div>
        )}
      </Modal>
    </div>
  )
}

const usePendingGridStyles = createStyles(({ css, token }) => ({
  container: css`
    height: 100%;
    display: flex;
    flex-direction: column;
  `,
  toolbar: css`
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,
  receiverSelect: css`
    width: 400px;
  `,
  errorAlert: css`
    margin-bottom: 16px;
    flex-shrink: 0;
  `,
  emptyState: css`
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  `,
  table: css`
    flex: 1;

    .ant-table-thead > tr > th {
      background: ${token.colorBgLayout};
      font-weight: 600;
    }
  `,
  className: css`
    font-family: monospace;
    font-size: 12px;
    color: ${token.colorTextSecondary};
  `,
  modalContent: css`
    max-height: 500px;
    overflow: auto;
  `,
  modalMeta: css`
    margin-bottom: 12px;
  `,
  codeBlock: css`
    pre {
      background: ${token.colorBgLayout};
      padding: 12px;
      border-radius: ${token.borderRadius}px;
      font-size: 12px;
      white-space: pre-wrap;
      word-break: break-all;
      max-height: 400px;
      overflow: auto;
    }
  `
}))

export default MessengerPendingGrid