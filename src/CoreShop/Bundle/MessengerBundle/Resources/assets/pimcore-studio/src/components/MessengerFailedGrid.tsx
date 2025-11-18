/**
 * MessengerFailedGrid Component
 *
 * TODO: ALL HARDCODED STRINGS NEED TRANSLATIONS
 * - Table column titles (ID, Class, Failed At, Error, Actions)
 * - Tooltip texts
 * - Modal titles
 * - Placeholder texts
 * - Button labels
 * - Message texts
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
  message,
  Typography,
  Tooltip
} from 'antd'
import { 
  InfoCircleOutlined, 
  ExclamationCircleOutlined, 
  DeleteOutlined, 
  RedoOutlined,
  ReloadOutlined
} from '@ant-design/icons'
import { ColumnsType } from 'antd/es/table'
import { useMessengerReceivers, useMessengerFailedMessages } from '../hooks/useMessenger'
import { MessengerFailedMessage } from '../types'

const { Option } = Select
const { Text, Paragraph } = Typography

export const MessengerFailedGrid: React.FC = () => {
  const { failureReceivers, loading: receiversLoading } = useMessengerReceivers()
  const [selectedReceiver, setSelectedReceiver] = useState<string | null>(null)
  const [infoModalOpen, setInfoModalOpen] = useState(false)
  const [errorModalOpen, setErrorModalOpen] = useState(false)
  const [selectedMessage, setSelectedMessage] = useState<MessengerFailedMessage | null>(null)
  const [processingActions, setProcessingActions] = useState<Set<string>>(new Set())

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
      message.success('Message deleted successfully')
    } catch (error) {
      message.error('Failed to delete message')
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
      message.success('Message retry initiated successfully')
    } catch (error) {
      message.error('Failed to retry message')
    } finally {
      setProcessingActions(prev => {
        const newSet = new Set(prev)
        newSet.delete(`retry-${messageId}`)
        return newSet
      })
    }
  }

  const columns: ColumnsType<MessengerFailedMessage> = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 100,
    },
    {
      title: 'Class',
      dataIndex: 'class',
      key: 'class',
      width: 400,
      ellipsis: true,
    },
    {
      title: 'Failed At',
      dataIndex: 'failed_at',
      key: 'failed_at',
      width: 150,
      render: (date: string) => {
        if (!date) return '-'
        return new Date(date).toLocaleDateString()
      },
    },
    {
      title: 'Error',
      dataIndex: 'error',
      key: 'error',
      ellipsis: true,
      render: (error: string) => (
        <Text type="danger" ellipsis={{ tooltip: error }}>
          {error}
        </Text>
      ),
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 150,
      render: (_, record) => (
        <Space size="small">
          <Tooltip title="Show message info">
            <Button
              size="small"
              icon={<InfoCircleOutlined />}
              onClick={() => handleInfoClick(record)}
            />
          </Tooltip>
          <Tooltip title="Show error details">
            <Button
              size="small"
              danger
              icon={<ExclamationCircleOutlined />}
              onClick={() => handleErrorClick(record)}
            />
          </Tooltip>
          <Popconfirm
            title="Delete message"
            description="Are you sure you want to delete this failed message?"
            onConfirm={() => handleDeleteClick(record)}
            okText="Delete"
            cancelText="Cancel"
          >
            <Button
              size="small"
              danger
              icon={<DeleteOutlined />}
              loading={processingActions.has(`delete-${record.id}`)}
            />
          </Popconfirm>
          <Popconfirm
            title="Retry message"
            description="Are you sure you want to retry this failed message?"
            onConfirm={() => handleRetryClick(record)}
            okText="Retry"
            cancelText="Cancel"
          >
            <Button
              size="small"
              type="primary"
              icon={<RedoOutlined />}
              loading={processingActions.has(`retry-${record.id}`)}
            />
          </Popconfirm>
        </Space>
      ),
    },
  ]

  return (
    <div>
      <div style={{ marginBottom: 16, display: 'flex', gap: 16, alignItems: 'center' }}>
        <Select
          style={{ width: 400 }}
          placeholder="Select failure receiver"
          value={selectedReceiver}
          onChange={handleReceiverChange}
          loading={receiversLoading}
          allowClear
        >
          {failureReceivers.map(receiver => (
            <Option key={receiver.receiver} value={receiver.receiver}>
              {receiver.receiver}
            </Option>
          ))}
        </Select>
        
        <Button 
          icon={<ReloadOutlined />} 
          onClick={reload}
          disabled={!selectedReceiver}
        >
          Reload
        </Button>
      </div>

      {error && (
        <Alert
          message="Error loading failed messages"
          description={error}
          type="error"
          style={{ marginBottom: 16 }}
          closable
        />
      )}

      <Table
        columns={columns}
        dataSource={messages}
        rowKey="id"
        loading={messagesLoading}
        // disabled={!selectedReceiver}
        scroll={{ y: 'calc(100vh - 450px)' }}
        pagination={false}
      />

      {/* Info Modal */}
      <Modal
        title="Message Information"
        open={infoModalOpen}
        onCancel={() => setInfoModalOpen(false)}
        footer={[
          <Button key="close" onClick={() => setInfoModalOpen(false)}>
            Close
          </Button>,
        ]}
        width={600}
      >
        {selectedMessage && (
          <div style={{ maxHeight: 400, overflow: 'auto' }}>
            <Paragraph>
              <pre style={{ whiteSpace: 'pre-wrap', wordBreak: 'break-all' }}>
                {selectedMessage.serialized || 'No serialized data available'}
              </pre>
            </Paragraph>
          </div>
        )}
      </Modal>

      {/* Error Modal */}
      <Modal
        title="Error Details"
        open={errorModalOpen}
        onCancel={() => setErrorModalOpen(false)}
        footer={[
          <Button key="close" onClick={() => setErrorModalOpen(false)}>
            Close
          </Button>,
        ]}
        width={600}
      >
        {selectedMessage && (
          <div style={{ maxHeight: 400, overflow: 'auto' }}>
            <Alert
              message="Error Information"
              description={
                <pre style={{ whiteSpace: 'pre-wrap', wordBreak: 'break-all' }}>
                  {selectedMessage.error || 'No error information available'}
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

export default MessengerFailedGrid