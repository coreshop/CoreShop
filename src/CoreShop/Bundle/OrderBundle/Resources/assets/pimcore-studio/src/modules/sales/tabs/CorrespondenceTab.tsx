/**
 * CoreShop OrderBundle Correspondence Tab
 *
 * Displays email correspondence history for orders.
 * Pattern from ExtJS: /order/detail/blocks/correspondence.js
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
import { Table, Button, Card, Empty, Modal } from 'antd'
import { createStyles } from 'antd-style'
import { MailOutlined, FileTextOutlined, MessageOutlined } from '@ant-design/icons'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps } from '../registry'
import { useSaleContext } from '../context/SaleActionsContext'

interface EmailCorrespondence {
  date: number
  subject: string
  recipient: string
  'email-log'?: number
  document?: number
  threadId?: number
}

export const CorrespondenceTab: React.FC<SaleTabProps> = () => {
  const { sale } = useSaleContext()
  const { styles } = useCorrespondenceTabStyles()
  const [emailLogModal, setEmailLogModal] = React.useState<number | null>(null)
  const [iframeKey, setIframeKey] = React.useState(0)

  if (!sale) return null

  const correspondence = ((sale as any).mailCorrespondence || []) as EmailCorrespondence[]

  // Open email log in modal
  const openEmailLog = (emailLogId: number) => {
    setEmailLogModal(emailLogId)
    setIframeKey(prev => prev + 1)
  }

  // Open email document in Pimcore
  const openEmailDocument = (documentId: number) => {
    // TODO: Implement Pimcore Document opening in Studio v2
    // In legacy admin: pimcore.helpers.openDocument(documentId, 'email')
    // Studio v2 needs API endpoint to open documents in the new editor
    alert(`TODO: Open Pimcore Email Document (ID: ${documentId})\n\nStudio v2 does not yet support opening Pimcore Documents.\nThis feature needs to be implemented similar to DataObject opening.`)
    console.warn('[CorrespondenceTab] TODO: Implement document opening for Studio v2', { documentId })
  }

  // Open messaging thread
  const openMessagingThread = (threadId: number) => {
    // Use CoreShop helper to open messaging thread
    if ((window as any).coreshop?.helpers?.openMessagingThread) {
      (window as any).coreshop.helpers.openMessagingThread(threadId)
    }
  }

  const columns: Array<ColumnType<EmailCorrespondence>> = [
    {
      title: 'Date',
      dataIndex: 'date',
      key: 'date',
      width: 180,
      render: (date) => formatDateTime(date)
    },
    {
      title: 'Subject',
      dataIndex: 'subject',
      key: 'subject',
      ellipsis: true
    },
    {
      title: 'Recipient',
      dataIndex: 'recipient',
      key: 'recipient',
      width: 250,
      ellipsis: true
    },
    {
      title: '',
      key: 'email-log',
      width: 50,
      align: 'center',
      render: (_, record) => {
        if (!record['email-log']) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<MailOutlined />}
            title="Show Email Log"
            onClick={() => openEmailLog(record['email-log']!)}
          />
        )
      }
    },
    {
      title: '',
      key: 'document',
      width: 50,
      align: 'center',
      render: (_, record) => {
        if (!record.document) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<FileTextOutlined />}
            title="Open Email Document"
            onClick={() => openEmailDocument(record.document!)}
          />
        )
      }
    },
    {
      title: '',
      key: 'thread',
      width: 50,
      align: 'center',
      render: (_, record) => {
        if (!record.threadId) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<MessageOutlined />}
            title="Open Messaging Thread"
            onClick={() => openMessagingThread(record.threadId!)}
          />
        )
      }
    }
  ]

  return (
    <>
      <Card
        title="Mail Correspondence"
        className={styles.card}
      >
        {correspondence.length === 0 ? (
          <Empty
            description="No email correspondence"
            image={Empty.PRESENTED_IMAGE_SIMPLE}
          />
        ) : (
          <Table
            dataSource={correspondence}
            columns={columns}
            rowKey={(record, index) => `${record.date}-${index}`}
            pagination={false}
            className={styles.table}
            size="small"
            scroll={{ y: 360 }}
          />
        )}
      </Card>

      {/* Email Log Modal */}
      {emailLogModal !== null && (
        <Modal
          title="Mail Correspondence"
          open={true}
          onCancel={() => setEmailLogModal(null)}
          footer={null}
          width={700}
          className={styles.modal}
        >
          <div className={styles.iframeContainer}>
            <iframe
              key={iframeKey}
              src={`/admin/email/show-email-log?id=${emailLogModal}&type=html`}
              className={styles.iframe}
              title="Email Log"
              sandbox="allow-same-origin allow-scripts"
            />
          </div>
        </Modal>
      )}
    </>
  )
}

const useCorrespondenceTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }
  `,
  table: css`
    .ant-table-thead > tr > th {
      background: ${token.colorBgContainer};
      font-weight: 600;
    }
  `,
  modal: css`
    .ant-modal-body {
      padding: 0;
    }
  `,
  iframeContainer: css`
    width: 100%;
    height: 500px;
    overflow: hidden;
  `,
  iframe: css`
    width: 100%;
    height: 100%;
    border: none;
  `
}))
