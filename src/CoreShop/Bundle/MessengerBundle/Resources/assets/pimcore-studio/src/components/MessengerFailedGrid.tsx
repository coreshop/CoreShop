/**
 * MessengerFailedGrid Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState } from 'react'
import {
  Table,
  Select,
  Button,
  Space,
  Modal,
  Alert,
  Popconfirm,
  Typography,
  Tooltip,
  Tag,
  Empty
} from 'antd'
import {
  InfoCircleOutlined,
  ExclamationCircleOutlined,
  DeleteOutlined,
  RedoOutlined,
  ReloadOutlined,
  WarningOutlined
} from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import { ColumnsType } from 'antd/es/table'
import { useMessengerReceivers, useMessengerFailedMessages } from '../hooks/useMessenger'
import { MessengerFailedMessage } from '../types'

const { Text, Paragraph } = Typography

export const MessengerFailedGrid: React.FC = () => {
  const { failureReceivers, loading: receiversLoading } = useMessengerReceivers()
  const [selectedReceiver, setSelectedReceiver] = useState<string | null>(null)
  const [infoModalOpen, setInfoModalOpen] = useState(false)
  const [errorModalOpen, setErrorModalOpen] = useState(false)
  const [selectedMessage, setSelectedMessage] = useState<MessengerFailedMessage | null>(null)
  const [processingActions, setProcessingActions] = useState<Set<string>>(new Set())
  const { styles } = useFailedGridStyles()
  const messageApi = useMessage()
  const { t } = useTranslation()

  const {
    messages,
    loading: messagesLoading,
    error,
    reload,
    deleteMessage,
    retryMessage
  } = useMessengerFailedMessages(selectedReceiver)

  const handleReceiverChange = (value: string) => {
    setSelectedReceiver(value)
  }

  const handleInfoClick = (record: MessengerFailedMessage) => {
    setSelectedMessage(record)
    setInfoModalOpen(true)
  }

  const handleErrorClick = (record: MessengerFailedMessage) => {
    setSelectedMessage(record)
    setErrorModalOpen(true)
  }

  const handleDeleteClick = async (record: MessengerFailedMessage) => {
    const messageId = record.id
    setProcessingActions(prev => new Set(prev).add(`delete-${messageId}`))

    try {
      await deleteMessage(messageId)
      void messageApi.success(t('coreshop_messenger_delete_success', { defaultValue: 'Message deleted successfully' }))
    } catch (err) {
      void messageApi.error(renderApiError(t('coreshop_messenger_delete_error', { defaultValue: 'Failed to delete message' })))
    } finally {
      setProcessingActions(prev => {
        const newSet = new Set(prev)
        newSet.delete(`delete-${messageId}`)
        return newSet
      })
    }
  }

  const handleRetryClick = async (record: MessengerFailedMessage) => {
    const messageId = record.id
    setProcessingActions(prev => new Set(prev).add(`retry-${messageId}`))

    try {
      await retryMessage(messageId)
      void messageApi.success(t('coreshop_messenger_retry_success', { defaultValue: 'Message retry initiated successfully' }))
    } catch (err) {
      void messageApi.error(renderApiError(t('coreshop_messenger_retry_error', { defaultValue: 'Failed to retry message' })))
    } finally {
      setProcessingActions(prev => {
        const newSet = new Set(prev)
        newSet.delete(`retry-${messageId}`)
        return newSet
      })
    }
  }

  // Extract short class name from full namespace
  const getShortClassName = (fullClass: string): string => {
    return fullClass.split('\\').pop() || fullClass
  }

  const columns: ColumnsType<MessengerFailedMessage> = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 80,
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
      title: t('coreshop_messenger_failed_at', { defaultValue: 'Failed At' }),
      dataIndex: 'failed_at',
      key: 'failed_at',
      width: 160,
      render: (date: string) => {
        if (!date) return '-'
        const d = new Date(date)
        return (
          <span className={styles.dateCell}>
            {d.toLocaleDateString()} {d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </span>
        )
      },
    },
    {
      title: t('coreshop_messenger_error', { defaultValue: 'Error' }),
      dataIndex: 'error',
      key: 'error',
      ellipsis: true,
      render: (errorText: string) => (
        <Text type="danger" ellipsis={{ tooltip: errorText }}>
          <WarningOutlined style={{ marginRight: 4 }} />
          {errorText}
        </Text>
      ),
    },
    {
      title: t('coreshop_messenger_actions', { defaultValue: 'Actions' }),
      key: 'actions',
      width: 160,
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
          <Tooltip title={t('coreshop_messenger_show_error', { defaultValue: 'Show error' })}>
            <Button
              size="small"
              danger
              icon={<ExclamationCircleOutlined />}
              onClick={() => handleErrorClick(record)}
            />
          </Tooltip>
          <Popconfirm
            title={t('coreshop_messenger_delete_failed_message', { defaultValue: 'Delete failed Message' })}
            description={t('coreshop_messenger_delete_confirm', { defaultValue: 'Are you sure you want to delete this message?' })}
            onConfirm={() => handleDeleteClick(record)}
            okText={t('coreshop_messenger_delete', { defaultValue: 'Delete' })}
            cancelText={t('coreshop_messenger_cancel', { defaultValue: 'Cancel' })}
          >
            <Tooltip title={t('coreshop_messenger_delete', { defaultValue: 'Delete' })}>
              <Button
                size="small"
                danger
                icon={<DeleteOutlined />}
                loading={processingActions.has(`delete-${record.id}`)}
              />
            </Tooltip>
          </Popconfirm>
          <Popconfirm
            title={t('coreshop_messenger_retry_failed_message', { defaultValue: 'Retry failed Message' })}
            description={t('coreshop_messenger_retry_confirm', { defaultValue: 'Are you sure you want to retry this message?' })}
            onConfirm={() => handleRetryClick(record)}
            okText={t('coreshop_messenger_retry', { defaultValue: 'Retry' })}
            cancelText={t('coreshop_messenger_cancel', { defaultValue: 'Cancel' })}
          >
            <Tooltip title={t('coreshop_messenger_retry', { defaultValue: 'Retry' })}>
              <Button
                size="small"
                type="primary"
                icon={<RedoOutlined />}
                loading={processingActions.has(`retry-${record.id}`)}
              />
            </Tooltip>
          </Popconfirm>
        </Space>
      ),
    },
  ]

  return (
    <div className={styles.container}>
      <div className={styles.toolbar}>
        <Select
          className={styles.receiverSelect}
          placeholder={t('coreshop_messenger_failure_receivers', { defaultValue: 'Select failure receiver' })}
          value={selectedReceiver}
          onChange={handleReceiverChange}
          loading={receiversLoading}
          allowClear
          showSearch
          filterOption={(input, option) =>
            (option?.children as unknown as string)?.toLowerCase().includes(input.toLowerCase())
          }
        >
          {failureReceivers.map(receiver => (
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
          description={t('coreshop_messenger_select_receiver', { defaultValue: 'Please select a receiver to view failed messages' })}
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

      {/* Error Modal */}
      <Modal
        title={t('coreshop_messenger_error_details', { defaultValue: 'Error Details' })}
        open={errorModalOpen}
        onCancel={() => setErrorModalOpen(false)}
        footer={[
          <Button key="close" onClick={() => setErrorModalOpen(false)}>
            {t('coreshop_messenger_close', { defaultValue: 'Close' })}
          </Button>,
        ]}
        width={700}
      >
        {selectedMessage && (
          <div className={styles.modalContent}>
            <Alert
              message={t('coreshop_messenger_error', { defaultValue: 'Error' })}
              description={
                <pre className={styles.errorPre}>
                  {selectedMessage.error || t('coreshop_messenger_no_error', { defaultValue: 'No error information available' })}
                </pre>
              }
              type="error"
              showIcon
            />
          </div>
        )}
      </Modal>
    </div>
  )
}

const useFailedGridStyles = createStyles(({ css, token }) => ({
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
  dateCell: css`
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
  `,
  errorPre: css`
    white-space: pre-wrap;
    word-break: break-all;
    font-size: 12px;
    margin: 0;
  `
}))

export default MessengerFailedGrid