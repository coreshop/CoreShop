/**
 * CoreShop OrderBundle Comments Tab
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
import { Card, Empty, Button, Modal, Input, Checkbox, Space } from 'antd'
import { createStyles } from 'antd-style'
import { PlusOutlined, DeleteOutlined, MailOutlined, UserOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import type { SaleTabProps } from '../registry'
import { useSaleContext } from '../context/SaleActionsContext'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

const { TextArea } = Input

interface Comment {
  id: number
  text: string
  date: number
  userName: string
  submitAsEmail: boolean
}

export const CommentsTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { sale, readonly } = useSaleContext()
  const { styles } = useCommentsTabStyles()
  const [isModalOpen, setIsModalOpen] = React.useState(false)
  const [newComment, setNewComment] = React.useState('')
  const [submitToCustomer, setSubmitToCustomer] = React.useState(false)
  const [comments, setComments] = React.useState<Comment[]>([])
  const [loading, setLoading] = React.useState(true)

  // Load comments from API
  React.useEffect(() => {
    if (!sale) return

    const loadComments = async () => {
      setLoading(true)
      try {
        const response = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
        const data = await response.json()
        setComments(data.comments || [])
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load comments')))
        setComments([])
      } finally {
        setLoading(false)
      }
    }

    void loadComments()
  }, [sale?.id])

  // Handle add comment
  const handleAddComment = async () => {
    if (!sale) return

    if (!newComment.trim()) {
      void messageApi.warning(t('coreshop_order_comment', { defaultValue: 'Comment' }))
      return
    }

    try {
      const formData = new URLSearchParams()
      formData.append('comment', newComment)
      formData.append('submitAsEmail', submitToCustomer ? 'true' : 'false')
      formData.append('id', String(sale.id))

      const response = await fetch('/pimcore-studio/api/coreshop/order-comment/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData.toString()
      })

      const data = await response.json()

      if (data.success) {
        void messageApi.success(t('coreshop_order_comment_added', { defaultValue: 'Comment added successfully' }))
        setIsModalOpen(false)
        setNewComment('')
        setSubmitToCustomer(false)

        // Reload comments
        const listResponse = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
        const listData = await listResponse.json()
        setComments(listData.comments || [])
      } else {
        void messageApi.error(renderApiError(t('coreshop_save_error', { defaultValue: 'Error saving item' })))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_save_error', { defaultValue: 'Error saving item' }))))
    }
  }

  // Handle delete comment
  const handleDeleteComment = (commentId: number) => {
    Modal.confirm({
      title: t('coreshop_delete_order_comment_confirm', { defaultValue: 'Do you really want to delete this Comment?' }),
      okText: t('coreshop_yes', { defaultValue: 'Yes' }),
      cancelText: t('coreshop_no', { defaultValue: 'No' }),
      onOk: async () => {
        try {
          const formData = new URLSearchParams()
          formData.append('id', String(commentId))

          const response = await fetch('/pimcore-studio/api/coreshop/order-comment/delete', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
          })

          const data = await response.json()

          if (data.success) {
            void messageApi.success(t('coreshop_order_comment', { defaultValue: 'Comment' }))

            // Reload comments
            if (sale) {
              const listResponse = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
              const listData = await listResponse.json()
              setComments(listData.comments || [])
            }
          } else {
            void messageApi.error(renderApiError(t('coreshop_save_error', { defaultValue: 'Error saving item' })))
          }
        } catch (error) {
          void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_save_error', { defaultValue: 'Error saving item' }))))
        }
      }
    })
  }

  const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
  }

  return (
    <>
      <Card
        title={t('coreshop_order_comments', { defaultValue: 'Comments' })}
        className={styles.card}
        extra={
          (
            <Button
              type="text"
              icon={<PlusOutlined />}
              onClick={() => setIsModalOpen(true)}
              size="small"
              title={t('coreshop_order_comment_create', { defaultValue: 'Create Comment' })}
            />
          )
        }
      >
        {comments.length === 0 ? (
          <div className={styles.emptyState}>
            <Empty
              description={t('coreshop_order_comments_nothing_found', { defaultValue: 'No Comments available' })}
              image={Empty.PRESENTED_IMAGE_SIMPLE}
            />
          </div>
        ) : (
          <div className={styles.commentsList}>
            {comments.map((comment) => (
              <div key={comment.id} className={styles.commentItem}>
                <div className={styles.commentAvatar}>
                  {comment.userName ? getInitials(comment.userName) : <UserOutlined />}
                </div>
                <div className={styles.commentBody}>
                  <div className={styles.commentHeader}>
                    <span className={styles.commentAuthor}>{comment.userName}</span>
                    <span className={styles.commentDate}>{formatDateTime(comment.date)}</span>
                    {!readonly && (
                      <Button
                        type="text"
                        danger
                        size="small"
                        icon={<DeleteOutlined style={{ fontSize: 12 }} />}
                        className={styles.deleteButton}
                        onClick={() => handleDeleteComment(comment.id)}
                      />
                    )}
                  </div>
                  <div className={styles.commentText}>{comment.text}</div>
                  {comment.submitAsEmail && (
                    <div className={styles.commentBadge}>
                      <MailOutlined style={{ fontSize: 11, marginRight: 4 }} />
                      {t('coreshop_order_comments_notification_applied', { defaultValue: 'Comment has been submitted to Customer' })}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </Card>

      {/* Add Comment Modal */}
      <Modal
        title={t('coreshop_order_comment_create', { defaultValue: 'Create Comment' })}
        open={isModalOpen}
        onOk={handleAddComment}
        onCancel={() => {
          setIsModalOpen(false)
          setNewComment('')
          setSubmitToCustomer(false)
        }}
        okText={t('coreshop_add', { defaultValue: 'Add' })}
        cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      >
        <Space direction="vertical" style={{ width: '100%' }} size="middle">
          <TextArea
            value={newComment}
            onChange={(e) => setNewComment(e.target.value)}
            placeholder={t('coreshop_order_comment', { defaultValue: 'Comment' })}
            rows={6}
            autoFocus
          />
          <Checkbox
            checked={submitToCustomer}
            onChange={(e) => setSubmitToCustomer(e.target.checked)}
          >
            {t('coreshop_order_comment_trigger_notifications', { defaultValue: 'Submit Comment to Customer' })}
          </Checkbox>
        </Space>
      </Modal>
    </>
  )
}

const useCommentsTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-body {
      padding: 0;
    }
  `,
  emptyState: css`
    padding: 24px 16px;
    text-align: center;

    .ant-empty {
      margin-block: 0;
    }
  `,
  commentsList: css`
    display: flex;
    flex-direction: column;
    max-height: 400px;
    overflow-y: auto;
  `,
  commentItem: css`
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
    transition: background 0.15s;

    &:last-child {
      border-bottom: none;
    }

    &:hover {
      background: ${token.colorBgLayout};

      .ant-btn-dangerous {
        opacity: 1;
      }
    }
  `,
  commentAvatar: css`
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: ${token.colorPrimary}12;
    color: ${token.colorPrimary};
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    flex-shrink: 0;
    margin-top: 2px;
  `,
  commentBody: css`
    flex: 1;
    min-width: 0;
  `,
  commentHeader: css`
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
  `,
  commentAuthor: css`
    font-size: 13px;
    font-weight: 600;
    color: ${token.colorText};
  `,
  commentDate: css`
    font-size: 11px;
    color: ${token.colorTextTertiary};
    flex: 1;
  `,
  deleteButton: css`
    opacity: 0;
    transition: opacity 0.15s;
  `,
  commentText: css`
    font-size: 13px;
    color: ${token.colorText};
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.5;
  `,
  commentBadge: css`
    display: inline-flex;
    align-items: center;
    font-size: 11px;
    color: ${token.colorPrimary};
    margin-top: 6px;
    padding: 2px 8px;
    background: ${token.colorPrimary}08;
    border-radius: ${token.borderRadiusSM}px;
  `
}))
