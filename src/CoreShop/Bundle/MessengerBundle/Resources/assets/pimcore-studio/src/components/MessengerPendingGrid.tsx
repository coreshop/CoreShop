/**
 * TODO: ALL HARDCODED STRINGS NEED TRANSLATIONS
 *
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

import React, { useState } from 'react'
import { 
  Table, 
  Select, 
  Button, 
  Space, 
  Modal, 
  Alert,
  Typography,
  Tooltip
} from 'antd'
import { 
  InfoCircleOutlined, 
  ReloadOutlined
} from '@ant-design/icons'
import { ColumnsType } from 'antd/es/table'
import { useMessengerReceivers, useMessengerMessages } from '../hooks/useMessenger'
import { MessengerMessage } from '../types'

const { Option } = Select
const { Paragraph } = Typography

export const MessengerPendingGrid: React.FC = () => {
  const { receivers, loading: receiversLoading } = useMessengerReceivers()
  const [selectedReceiver, setSelectedReceiver] = useState<string | null>(null)
  const [infoModalOpen, setInfoModalOpen] = useState(false)
  const [selectedMessage, setSelectedMessage] = useState<MessengerMessage | null>(null)

  const { 
    messages, 
    loading: messagesLoading, 
    error, 
    reload
  } = useMessengerMessages(selectedReceiver)

  const handleReceiverChange = (value: string) => {
    setSelectedReceiver(value)
  }

  const handleInfoClick = (record: MessengerMessage) => {
    setSelectedMessage(record)
    setInfoModalOpen(true)
  }

  const columns: ColumnsType<MessengerMessage> = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 150,
    },
    {
      title: 'Class',
      dataIndex: 'class',
      key: 'class',
      ellipsis: true,
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 80,
      render: (_, record) => (
        <Space size="small">
          <Tooltip title="Show message info">
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
    <div>
      <div style={{ marginBottom: 16, display: 'flex', gap: 16, alignItems: 'center' }}>
        <Select
          style={{ width: 400 }}
          placeholder="Select receiver"
          value={selectedReceiver}
          onChange={handleReceiverChange}
          loading={receiversLoading}
          allowClear
        >
          {receivers.map(receiver => (
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
          message="Error loading pending messages"
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
    </div>
  )
}

export default MessengerPendingGrid