/**
 * MessengerOverview Component - Queue overview with all queues at a glance
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
import { useState, useEffect, useCallback } from 'react'
import {
  Table,
  Button,
  Space,
  Tag,
  Badge,
  Tooltip,
  Spin,
  Alert,
  Modal,
  Popconfirm,
  message,
  Typography
} from 'antd'
import {
  ReloadOutlined,
  CheckCircleOutlined,
  WarningOutlined,
  ClockCircleOutlined,
  InfoCircleOutlined,
  ExclamationCircleOutlined,
  DeleteOutlined,
  RedoOutlined
} from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useTranslation } from 'react-i18next'
import { ColumnsType } from 'antd/es/table'
import { messengerService } from '../services/messenger'
import { MessengerFailedMessage, MessengerMessage } from '../types'
import { messengerEventEmitter, type MessengerUpdateEvent } from '../modules/mercure/messenger-event-emitter'

const { Text, Paragraph } = Typography

interface QueueData {
  key: string
  name: string
  shortName: string
  pendingCount: number
  failedCount: number
  failureReceiver: string | null
}

interface ExpandedData {
  pending: MessengerMessage[]
  failed: MessengerFailedMessage[]
  loading: boolean
}

export const MessengerOverview: React.FC = () => {
  const [queues, setQueues] = useState<QueueData[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [expandedKeys, setExpandedKeys] = useState<string[]>([])
  const [expandedData, setExpandedData] = useState<Record<string, ExpandedData>>({})
  const [selectedMessage, setSelectedMessage] = useState<MessengerMessage | MessengerFailedMessage | null>(null)
  const [infoModalOpen, setInfoModalOpen] = useState(false)
  const [errorModalOpen, setErrorModalOpen] = useState(false)
  const [processingActions, setProcessingActions] = useState<Set<string>>(new Set())
  const { styles, theme } = useOverviewStyles()
  const { t } = useTranslation()

  const loadQueues = useCallback(async () => {
    try {
      setLoading(true)
      setError(null)

      const [counts, receivers, failureReceivers] = await Promise.all([
        messengerService.getMessageCount(),
        messengerService.getReceivers(),
        messengerService.getFailureReceivers()
      ])

      // Build queue map with counts
      const queueMap = new Map<string, QueueData>()

      // Add receivers
      receivers.forEach(r => {
        queueMap.set(r.receiver, {
          key: r.receiver,
          name: r.receiver,
          shortName: r.receiver.replace('coreshop_', '').replace('_failed', ''),
          pendingCount: 0,
          failedCount: 0,
          failureReceiver: null
        })
      })

      // Add failure receivers and link them
      failureReceivers.forEach(fr => {
        const baseName = fr.receiver.replace('_failed', '')
        if (queueMap.has(baseName)) {
          queueMap.get(baseName)!.failureReceiver = fr.receiver
        } else {
          queueMap.set(fr.receiver, {
            key: fr.receiver,
            name: fr.receiver,
            shortName: fr.receiver.replace('coreshop_', '').replace('_failed', ''),
            pendingCount: 0,
            failedCount: 0,
            failureReceiver: fr.receiver
          })
        }
      })

      // Add counts from the count API (this shows pending messages)
      counts.forEach(c => {
        if (queueMap.has(c.receiver)) {
          queueMap.get(c.receiver)!.pendingCount = c.count
        }
      })

      // Load failed counts for each failure receiver
      const failedCountPromises = failureReceivers.map(async fr => {
        try {
          const failed = await messengerService.getFailedMessages(fr.receiver)
          return { receiver: fr.receiver, count: failed.length }
        } catch {
          return { receiver: fr.receiver, count: 0 }
        }
      })

      const failedCounts = await Promise.all(failedCountPromises)
      failedCounts.forEach(fc => {
        // Find queue with this failure receiver
        for (const queue of queueMap.values()) {
          if (queue.failureReceiver === fc.receiver) {
            queue.failedCount = fc.count
            break
          }
        }
      })

      setQueues(Array.from(queueMap.values()).sort((a, b) => {
        // Sort by failed count desc, then pending count desc
        if (b.failedCount !== a.failedCount) return b.failedCount - a.failedCount
        return b.pendingCount - a.pendingCount
      }))
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load queues')
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadQueues()
  }, [loadQueues])

  // Subscribe to Mercure updates
  useEffect(() => {
    const unsubscribe = messengerEventEmitter.subscribe((_event: MessengerUpdateEvent) => {
      // Reload overview on any messenger event
      loadQueues()
    })
    return unsubscribe
  }, [loadQueues])

  const loadExpandedData = async (queue: QueueData) => {
    setExpandedData(prev => ({
      ...prev,
      [queue.key]: { pending: [], failed: [], loading: true }
    }))

    try {
      const [pending, failed] = await Promise.all([
        messengerService.getMessages(queue.name).catch(() => []),
        queue.failureReceiver
          ? messengerService.getFailedMessages(queue.failureReceiver).catch(() => [])
          : Promise.resolve([])
      ])

      setExpandedData(prev => ({
        ...prev,
        [queue.key]: { pending, failed, loading: false }
      }))
    } catch {
      setExpandedData(prev => ({
        ...prev,
        [queue.key]: { pending: [], failed: [], loading: false }
      }))
    }
  }

  const handleExpand = (expanded: boolean, record: QueueData) => {
    if (expanded) {
      setExpandedKeys(prev => [...prev, record.key])
      loadExpandedData(record)
    } else {
      setExpandedKeys(prev => prev.filter(k => k !== record.key))
    }
  }

  const handleDeleteClick = async (queue: QueueData, msg: MessengerFailedMessage) => {
    if (!queue.failureReceiver) return

    const actionKey = `delete-${msg.id}`
    setProcessingActions(prev => new Set(prev).add(actionKey))

    try {
      await messengerService.deleteFailedMessage(queue.failureReceiver, msg.id)
      message.success(t('coreshop_messenger_delete_success', { defaultValue: 'Message deleted' }))
      loadExpandedData(queue)
      loadQueues()
    } catch {
      message.error(t('coreshop_messenger_delete_error', { defaultValue: 'Failed to delete' }))
    } finally {
      setProcessingActions(prev => {
        const newSet = new Set(prev)
        newSet.delete(actionKey)
        return newSet
      })
    }
  }

  const handleRetryClick = async (queue: QueueData, msg: MessengerFailedMessage) => {
    if (!queue.failureReceiver) return

    const actionKey = `retry-${msg.id}`
    setProcessingActions(prev => new Set(prev).add(actionKey))

    try {
      await messengerService.retryFailedMessage(queue.failureReceiver, msg.id)
      message.success(t('coreshop_messenger_retry_success', { defaultValue: 'Retry initiated' }))
      loadExpandedData(queue)
      loadQueues()
    } catch {
      message.error(t('coreshop_messenger_retry_error', { defaultValue: 'Failed to retry' }))
    } finally {
      setProcessingActions(prev => {
        const newSet = new Set(prev)
        newSet.delete(actionKey)
        return newSet
      })
    }
  }

  const getShortClassName = (fullClass: string): string => {
    return fullClass.split('\\').pop() || fullClass
  }

  const columns: ColumnsType<QueueData> = [
    {
      title: t('coreshop_messenger_queue', { defaultValue: 'Queue' }),
      dataIndex: 'shortName',
      key: 'name',
      render: (shortName: string, record) => (
        <Tooltip title={record.name}>
          <span className={styles.queueName}>{shortName}</span>
        </Tooltip>
      )
    },
    {
      title: t('coreshop_messenger_pending', { defaultValue: 'Pending' }),
      dataIndex: 'pendingCount',
      key: 'pending',
      width: 120,
      align: 'center',
      render: (count: number) => (
        <Badge
          count={count}
          showZero
          color={count === 0 ? theme.colorSuccess : theme.colorWarning}
          overflowCount={9999}
        />
      )
    },
    {
      title: t('coreshop_messenger_failed', { defaultValue: 'Failed' }),
      dataIndex: 'failedCount',
      key: 'failed',
      width: 120,
      align: 'center',
      render: (count: number) => (
        <Badge
          count={count}
          showZero
          color={count === 0 ? theme.colorSuccess : theme.colorError}
          overflowCount={9999}
        />
      )
    },
    {
      title: t('coreshop_messenger_status', { defaultValue: 'Status' }),
      key: 'status',
      width: 100,
      align: 'center',
      render: (_, record) => {
        if (record.failedCount > 0) {
          return <Tag icon={<WarningOutlined />} color="error">Issues</Tag>
        }
        if (record.pendingCount > 0) {
          return <Tag icon={<ClockCircleOutlined />} color="processing">Active</Tag>
        }
        return <Tag icon={<CheckCircleOutlined />} color="success">OK</Tag>
      }
    }
  ]

  const expandedRowRender = (record: QueueData) => {
    const data = expandedData[record.key]

    if (!data || data.loading) {
      return (
        <div className={styles.expandedLoading}>
          <Spin size="small" />
        </div>
      )
    }

    const hasFailed = data.failed.length > 0
    const hasPending = data.pending.length > 0

    if (!hasFailed && !hasPending) {
      return (
        <div className={styles.expandedEmpty}>
          {t('coreshop_messenger_no_messages', { defaultValue: 'No messages in this queue' })}
        </div>
      )
    }

    return (
      <div className={styles.expandedContent}>
        {hasFailed && (
          <div className={styles.expandedSection}>
            <div className={styles.sectionHeader}>
              <WarningOutlined style={{ color: theme.colorError }} />
              <span>{t('coreshop_messenger_failed_messages', { defaultValue: 'Failed Messages' })} ({data.failed.length})</span>
            </div>
            <Table
              size="small"
              dataSource={data.failed}
              rowKey="id"
              pagination={false}
              scroll={{ y: 200 }}
              columns={[
                {
                  title: 'ID',
                  dataIndex: 'id',
                  width: 60,
                  render: (id: string) => <Tag>{id}</Tag>
                },
                {
                  title: t('coreshop_messenger_class', { defaultValue: 'Class' }),
                  dataIndex: 'class',
                  ellipsis: true,
                  render: (cls: string) => (
                    <Tooltip title={cls}>
                      <span className={styles.className}>{getShortClassName(cls)}</span>
                    </Tooltip>
                  )
                },
                {
                  title: t('coreshop_messenger_error', { defaultValue: 'Error' }),
                  dataIndex: 'error',
                  ellipsis: true,
                  render: (err: string) => (
                    <Text type="danger" ellipsis={{ tooltip: err }}>{err}</Text>
                  )
                },
                {
                  title: t('coreshop_messenger_actions', { defaultValue: 'Actions' }),
                  width: 140,
                  render: (_, msg: MessengerFailedMessage) => (
                    <Space size="small">
                      <Tooltip title={t('coreshop_messenger_info', { defaultValue: 'Details' })}>
                        <Button
                          size="small"
                          icon={<InfoCircleOutlined />}
                          onClick={() => { setSelectedMessage(msg); setInfoModalOpen(true) }}
                        />
                      </Tooltip>
                      <Tooltip title={t('coreshop_messenger_show_error', { defaultValue: 'Error' })}>
                        <Button
                          size="small"
                          danger
                          icon={<ExclamationCircleOutlined />}
                          onClick={() => { setSelectedMessage(msg); setErrorModalOpen(true) }}
                        />
                      </Tooltip>
                      <Popconfirm
                        title={t('coreshop_messenger_delete_confirm', { defaultValue: 'Delete?' })}
                        onConfirm={() => handleDeleteClick(record, msg)}
                      >
                        <Button
                          size="small"
                          danger
                          icon={<DeleteOutlined />}
                          loading={processingActions.has(`delete-${msg.id}`)}
                        />
                      </Popconfirm>
                      <Popconfirm
                        title={t('coreshop_messenger_retry_confirm', { defaultValue: 'Retry?' })}
                        onConfirm={() => handleRetryClick(record, msg)}
                      >
                        <Button
                          size="small"
                          type="primary"
                          icon={<RedoOutlined />}
                          loading={processingActions.has(`retry-${msg.id}`)}
                        />
                      </Popconfirm>
                    </Space>
                  )
                }
              ]}
            />
          </div>
        )}

        {hasPending && (
          <div className={styles.expandedSection}>
            <div className={styles.sectionHeader}>
              <ClockCircleOutlined style={{ color: theme.colorWarning }} />
              <span>{t('coreshop_messenger_pending_messages', { defaultValue: 'Pending Messages' })} ({data.pending.length})</span>
            </div>
            <Table
              size="small"
              dataSource={data.pending}
              rowKey="id"
              pagination={false}
              scroll={{ y: 200 }}
              columns={[
                {
                  title: 'ID',
                  dataIndex: 'id',
                  width: 60,
                  render: (id: string) => <Tag>{id}</Tag>
                },
                {
                  title: t('coreshop_messenger_class', { defaultValue: 'Class' }),
                  dataIndex: 'class',
                  ellipsis: true,
                  render: (cls: string) => (
                    <Tooltip title={cls}>
                      <span className={styles.className}>{getShortClassName(cls)}</span>
                    </Tooltip>
                  )
                },
                {
                  title: t('coreshop_messenger_actions', { defaultValue: 'Actions' }),
                  width: 60,
                  render: (_, msg: MessengerMessage) => (
                    <Tooltip title={t('coreshop_messenger_info', { defaultValue: 'Details' })}>
                      <Button
                        size="small"
                        icon={<InfoCircleOutlined />}
                        onClick={() => { setSelectedMessage(msg); setInfoModalOpen(true) }}
                      />
                    </Tooltip>
                  )
                }
              ]}
            />
          </div>
        )}
      </div>
    )
  }

  // Summary stats
  const totalPending = queues.reduce((sum, q) => sum + q.pendingCount, 0)
  const totalFailed = queues.reduce((sum, q) => sum + q.failedCount, 0)
  const healthyQueues = queues.filter(q => q.failedCount === 0 && q.pendingCount === 0).length

  if (error) {
    return (
      <Alert
        message={t('coreshop_messenger_error_loading', { defaultValue: 'Error loading data' })}
        description={error}
        type="error"
        action={<Button onClick={loadQueues}>{t('coreshop_messenger_retry', { defaultValue: 'Retry' })}</Button>}
      />
    )
  }

  return (
    <div className={styles.container}>
      {/* Summary Header */}
      <div className={styles.summaryHeader}>
        <div className={styles.summaryStats}>
          <div className={styles.statItem}>
            <span className={styles.statValue}>{queues.length}</span>
            <span className={styles.statLabel}>{t('coreshop_messenger_queues', { defaultValue: 'Queues' })}</span>
          </div>
          <div className={styles.statDivider} />
          <div className={styles.statItem}>
            <span className={styles.statValue} style={{ color: totalPending > 0 ? theme.colorWarning : theme.colorSuccess }}>
              {totalPending}
            </span>
            <span className={styles.statLabel}>{t('coreshop_messenger_pending', { defaultValue: 'Pending' })}</span>
          </div>
          <div className={styles.statDivider} />
          <div className={styles.statItem}>
            <span className={styles.statValue} style={{ color: totalFailed > 0 ? theme.colorError : theme.colorSuccess }}>
              {totalFailed}
            </span>
            <span className={styles.statLabel}>{t('coreshop_messenger_failed', { defaultValue: 'Failed' })}</span>
          </div>
          <div className={styles.statDivider} />
          <div className={styles.statItem}>
            <span className={styles.statValue} style={{ color: theme.colorSuccess }}>
              {healthyQueues}
            </span>
            <span className={styles.statLabel}>{t('coreshop_messenger_healthy', { defaultValue: 'Healthy' })}</span>
          </div>
        </div>
        <Button
          type="primary"
          icon={<ReloadOutlined />}
          onClick={loadQueues}
          loading={loading}
        >
          {t('coreshop_messenger_reload', { defaultValue: 'Reload' })}
        </Button>
      </div>

      {/* Queue Table */}
      <Table
        columns={columns}
        dataSource={queues}
        rowKey="key"
        loading={loading}
        pagination={false}
        size="middle"
        className={styles.table}
        expandable={{
          expandedRowRender,
          expandedRowKeys: expandedKeys,
          onExpand: handleExpand,
          rowExpandable: (record) => record.pendingCount > 0 || record.failedCount > 0
        }}
      />

      {/* Info Modal */}
      <Modal
        title={t('coreshop_messenger_message_info', { defaultValue: 'Message Information' })}
        open={infoModalOpen}
        onCancel={() => setInfoModalOpen(false)}
        footer={<Button onClick={() => setInfoModalOpen(false)}>{t('coreshop_messenger_close', { defaultValue: 'Close' })}</Button>}
        width={700}
      >
        {selectedMessage && (
          <div className={styles.modalContent}>
            <div className={styles.modalMeta}>
              <Tag>ID: {selectedMessage.id}</Tag>
              <Tag color="blue">{getShortClassName(selectedMessage.class)}</Tag>
            </div>
            <Paragraph className={styles.codeBlock}>
              <pre>{selectedMessage.serialized || t('coreshop_messenger_no_data', { defaultValue: 'No data' })}</pre>
            </Paragraph>
          </div>
        )}
      </Modal>

      {/* Error Modal */}
      <Modal
        title={t('coreshop_messenger_error_details', { defaultValue: 'Error Details' })}
        open={errorModalOpen}
        onCancel={() => setErrorModalOpen(false)}
        footer={<Button onClick={() => setErrorModalOpen(false)}>{t('coreshop_messenger_close', { defaultValue: 'Close' })}</Button>}
        width={700}
      >
        {selectedMessage && 'error' in selectedMessage && (
          <Alert
            message={t('coreshop_messenger_error', { defaultValue: 'Error' })}
            description={<pre className={styles.errorPre}>{selectedMessage.error}</pre>}
            type="error"
            showIcon
          />
        )}
      </Modal>
    </div>
  )
}

const useOverviewStyles = createStyles(({ css, token }) => ({
  container: css`
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  `,
  summaryHeader: css`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: ${token.colorBgContainer};
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: ${token.borderRadius}px;
    margin-bottom: 16px;
    flex-shrink: 0;
  `,
  summaryStats: css`
    display: flex;
    align-items: center;
    gap: 24px;
  `,
  statItem: css`
    display: flex;
    flex-direction: column;
    align-items: center;
  `,
  statValue: css`
    font-size: 24px;
    font-weight: 600;
    line-height: 1;
  `,
  statLabel: css`
    font-size: 12px;
    color: ${token.colorTextSecondary};
    margin-top: 4px;
  `,
  statDivider: css`
    width: 1px;
    height: 40px;
    background: ${token.colorBorderSecondary};
  `,
  table: css`
    flex: 1;
    overflow: auto;

    .ant-table-thead > tr > th {
      background: ${token.colorBgLayout};
      font-weight: 600;
    }

    .ant-table-expanded-row > td {
      padding: 0 !important;
      background: ${token.colorBgLayout};
    }
  `,
  queueName: css`
    font-family: monospace;
    font-weight: 500;
  `,
  className: css`
    font-family: monospace;
    font-size: 12px;
    color: ${token.colorTextSecondary};
  `,
  expandedLoading: css`
    padding: 24px;
    text-align: center;
  `,
  expandedEmpty: css`
    padding: 24px;
    text-align: center;
    color: ${token.colorTextSecondary};
  `,
  expandedContent: css`
    padding: 12px 16px;
  `,
  expandedSection: css`
    &:not(:last-child) {
      margin-bottom: 16px;
    }
  `,
  sectionHeader: css`
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 13px;
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

export default MessengerOverview
