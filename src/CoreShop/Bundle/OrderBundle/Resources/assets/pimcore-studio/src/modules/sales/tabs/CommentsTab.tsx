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
import { Card, Empty, Button, Modal, Input, message, Checkbox, Space } from 'antd'
import { createStyles } from 'antd-style'
import { PlusOutlined, CloseCircleOutlined, MessageOutlined } from '@ant-design/icons'
import type { SaleTabProps } from '../registry'

const { TextArea } = Input

interface Comment {
  id: number
  text: string
  date: number
  userName: string
  submitAsEmail: boolean
}

export const CommentsTab: React.FC<SaleTabProps> = ({ sale, onChange, readonly }) => {
  const { styles } = useCommentsTabStyles()
  const [isModalOpen, setIsModalOpen] = React.useState(false)
  const [newComment, setNewComment] = React.useState('')
  const [submitToCustomer, setSubmitToCustomer] = React.useState(false)
  const [comments, setComments] = React.useState<Comment[]>([])
  const [loading, setLoading] = React.useState(true)

  // Load comments from API
  React.useEffect(() => {
    const loadComments = async () => {
      setLoading(true)
      try {
        // TODO: Replace with actual API endpoint
        const response = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
        const data = await response.json()
        setComments(data.comments || [])
      } catch (error) {
        console.error('Failed to load comments:', error)
        setComments([])
      } finally {
        setLoading(false)
      }
    }

    void loadComments()
  }, [sale.id])

  // Format date
  const formatDate = (date?: string | number) => {
    if (!date) return '-'
    const dateValue = typeof date === 'number' ? date * 1000 : date
    return new Date(dateValue).toLocaleString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  // Handle add comment
  const handleAddComment = async () => {
    if (!newComment.trim()) {
      void message.warning('Please enter a comment')
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
        void message.success('Comment added successfully')
        setIsModalOpen(false)
        setNewComment('')
        setSubmitToCustomer(false)

        // Reload comments
        const listResponse = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
        const listData = await listResponse.json()
        setComments(listData.comments || [])
      } else {
        void message.error('Failed to add comment')
      }
    } catch (error) {
      console.error('Error adding comment:', error)
      void message.error('Failed to add comment')
    }
  }

  // Handle delete comment
  const handleDeleteComment = (commentId: number) => {
    Modal.confirm({
      title: 'Delete Comment',
      content: 'Are you sure you want to delete this comment?',
      okText: 'Yes',
      cancelText: 'No',
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
            void message.success('Comment deleted')

            // Reload comments
            const listResponse = await fetch(`/pimcore-studio/api/coreshop/order-comment/list?id=${sale.id}`)
            const listData = await listResponse.json()
            setComments(listData.comments || [])
          } else {
            void message.error('Failed to delete comment')
          }
        } catch (error) {
          console.error('Error deleting comment:', error)
          void message.error('Failed to delete comment')
        }
      }
    })
  }

  return (
    <>
      <Card
        title="Comments"
        className={styles.card}
        extra={
          !readonly && (
            <Button
              type="text"
              icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
              onClick={() => setIsModalOpen(true)}
              title="Add Comment"
            />
          )
        }
      >
        {comments.length === 0 ? (
          <div className={styles.emptyState}>
            <Empty
              description="No Comments available"
              image={Empty.PRESENTED_IMAGE_SIMPLE}
            />
          </div>
        ) : (
          <div className={styles.commentsList}>
            {comments.map((comment) => (
              <div key={comment.id} className={styles.commentItem}>
                <div className={styles.commentHeader}>
                  <div className={styles.commentMeta}>
                    <span className={styles.commentDate}>
                      {formatDate(comment.date)} - published by {comment.userName}
                    </span>
                  </div>
                  {!readonly && (
                    <Button
                      type="text"
                      danger
                      size="small"
                      icon={<CloseCircleOutlined />}
                      onClick={() => handleDeleteComment(comment.id)}
                    />
                  )}
                </div>
                <div className={styles.commentText}>{comment.text}</div>
                {comment.submitAsEmail && (
                  <div className={styles.commentBadge}>
                    <MessageOutlined style={{ fontSize: 12, marginRight: 4 }} />
                    Comment has been submitted to Customer
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </Card>

      {/* Add Comment Modal */}
      <Modal
        title="Add Comment"
        open={isModalOpen}
        onOk={handleAddComment}
        onCancel={() => {
          setIsModalOpen(false)
          setNewComment('')
          setSubmitToCustomer(false)
        }}
        okText="Add"
        cancelText="Cancel"
      >
        <Space direction="vertical" style={{ width: '100%' }} size="middle">
          <TextArea
            value={newComment}
            onChange={(e) => setNewComment(e.target.value)}
            placeholder="Enter your comment..."
            rows={6}
            autoFocus
          />
          <Checkbox
            checked={submitToCustomer}
            onChange={(e) => setSubmitToCustomer(e.target.checked)}
          >
            Submit to Customer
          </Checkbox>
        </Space>
      </Modal>
    </>
  )
}

const useCommentsTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }
  `,
  emptyState: css`
    padding: 40px 20px;
    text-align: center;
    color: ${token.colorTextTertiary};
    font-style: italic;
  `,
  commentsList: css`
    display: flex;
    flex-direction: column;
    max-height: 400px;
    overflow-y: auto;
  `,
  commentItem: css`
    padding: 16px;
    border-bottom: 1px dashed ${token.colorBorder};

    &:last-child {
      border-bottom: none;
    }
  `,
  commentHeader: css`
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
  `,
  commentMeta: css`
    flex: 1;
  `,
  commentDate: css`
    font-size: 13px;
    color: ${token.colorTextSecondary};
    font-style: italic;
  `,
  commentText: css`
    font-size: 14px;
    color: ${token.colorText};
    margin-bottom: 8px;
    white-space: pre-wrap;
    word-wrap: break-word;
  `,
  commentBadge: css`
    display: flex;
    align-items: center;
    font-size: 12px;
    color: #722ed1;
    margin-top: 8px;
  `,
  commentBadgeAdmin: css`
    display: flex;
    align-items: center;
    font-size: 12px;
    color: ${token.colorTextSecondary};
    margin-top: 8px;
  `
}))
