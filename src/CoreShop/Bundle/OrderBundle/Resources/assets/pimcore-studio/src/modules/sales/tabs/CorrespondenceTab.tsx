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
import { useTableCardStyles } from '../styles/useTableCardStyles'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import { useTranslation } from 'react-i18next'
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
  const { t } = useTranslation()
  const { sale } = useSaleContext()
  const { styles: sharedStyles } = useTableCardStyles()
  const { styles: localStyles } = useCorrespondenceTabStyles()
  const styles = { ...sharedStyles, ...localStyles }
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
    alert(
      `${t('coreshop_mail_correspondence_open_document', { defaultValue: 'Open Email Document' })} (ID: ${documentId})\n\n` +
      t('coreshop_mail_correspondence_open_document_not_supported', {
        defaultValue: 'Studio v2 does not yet support opening Pimcore Documents.'
      })
    )
    console.warn('[CorrespondenceTab] TODO: Implement document opening for Studio v2', { documentId })
  }

  // Open messaging thread
  const openMessagingThread = (threadId: number) => {
    if ((window as any).coreshop?.helpers?.openMessagingThread) {
      (window as any).coreshop.helpers.openMessagingThread(threadId)
    }
  }

  const columns: Array<ColumnType<EmailCorrespondence>> = [
    {
      title: t('coreshop_date', { defaultValue: 'Date' }),
      dataIndex: 'date',
      key: 'date',
      width: 180,
      render: (date) => <span className={styles.dimText}>{formatDateTime(date)}</span>
    },
    {
      title: t('coreshop_mail_correspondence_subject', { defaultValue: 'Subject' }),
      dataIndex: 'subject',
      key: 'subject',
      ellipsis: true,
      render: (value) => <span style={{ fontWeight: 500 }}>{value}</span>
    },
    {
      title: t('coreshop_mail_correspondence_recipient', { defaultValue: 'Recipient' }),
      dataIndex: 'recipient',
      key: 'recipient',
      width: 250,
      ellipsis: true
    },
    {
      title: '',
      key: 'email-log',
      width: 36,
      align: 'center',
      render: (_, record) => {
        if (!record['email-log']) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<MailOutlined />}
            title={t('coreshop_mail_correspondence_mail_log_show', { defaultValue: 'Show sent mail log' })}
            onClick={() => openEmailLog(record['email-log']!)}
          />
        )
      }
    },
    {
      title: '',
      key: 'document',
      width: 36,
      align: 'center',
      render: (_, record) => {
        if (!record.document) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<FileTextOutlined />}
            title={t('coreshop_mail_correspondence_open_document', { defaultValue: 'Open Email Document' })}
            onClick={() => openEmailDocument(record.document!)}
          />
        )
      }
    },
    {
      title: '',
      key: 'thread',
      width: 36,
      align: 'center',
      render: (_, record) => {
        if (!record.threadId) return null

        return (
          <Button
            type="text"
            size="small"
            icon={<MessageOutlined />}
            title={t('coreshop_mail_correspondence_open_thread', { defaultValue: 'Open Messaging Thread' })}
            onClick={() => openMessagingThread(record.threadId!)}
          />
        )
      }
    }
  ]

  return (
    <>
      <Card
        title={t('coreshop_mail_correspondence', { defaultValue: 'Mail Correspondence' })}
        className={styles.card}
      >
        {correspondence.length === 0 ? (
          <Empty
            description={t('coreshop_mail_correspondence_none', { defaultValue: 'No email correspondence' })}
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
            scroll={{ x: 'max-content' }}
          />
        )}
      </Card>

      {/* Email Log Modal */}
      {emailLogModal !== null && (
        <Modal
          title={t('coreshop_mail_correspondence', { defaultValue: 'Mail correspondence' })}
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
              title={t('coreshop_mail_correspondence_mail_log', { defaultValue: 'Mail Log' })}
              sandbox="allow-same-origin allow-scripts"
            />
          </div>
        </Modal>
      )}
    </>
  )
}

const useCorrespondenceTabStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal-body {
      padding: 0;
    }
  `,
  iframeContainer: css`
    width: 100%;
    height: 500px;
    overflow: hidden;
    border-radius: 0 0 ${token.borderRadiusLG}px ${token.borderRadiusLG}px;
  `,
  iframe: css`
    width: 100%;
    height: 100%;
    border: none;
  `
}))
