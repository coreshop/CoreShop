/**
 * CoreShop OrderBundle State Change Modal
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
import { Modal, Button, Space } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { createStyles } from 'antd-style'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface Transition {
  transition: string
  label: string
  color: string
}

interface StateChangeModalProps {
  open: boolean
  title?: string
  description?: string
  transitions: Transition[]
  url: string
  id: number
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Reusable State Change Modal
 *
 * Used for changing states of:
 * - Payments
 * - Shipments
 * - Invoices
 *
 * Pattern from ExtJS: /order/state/changeState.js
 */
export const StateChangeModal: React.FC<StateChangeModalProps> = ({
  open,
  title = 'Change State',
  description = 'Select a transition to apply',
  transitions,
  url,
  id,
  onSuccess,
  onCancel
}) => {
  const { styles } = useStateChangeModalStyles()
  const messageApi = useMessage()
  const [loading, setLoading] = React.useState<string | null>(null)

  const handleTransition = async (transition: string) => {
    setLoading(transition)

    try {
      const params = new URLSearchParams()
      params.append('id', String(id))
      params.append('transition', transition)

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
      })

      const data = await response.json()

      if (data.success) {
        void messageApi.success('State changed successfully')
        onSuccess()
      } else {
        void messageApi.error(renderApiError(data.message || 'Failed to change state'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to change state')))
    } finally {
      setLoading(null)
    }
  }

  return (
    <Modal
      open={open}
      title={title}
      onCancel={onCancel}
      footer={null}
      width={450}
    >
      <div className={styles.content}>
        <div className={styles.description}>{description}</div>

        <Space direction="horizontal" wrap className={styles.buttonContainer}>
          {transitions.map((trans) => (
            <Button
              key={trans.transition}
              className={styles.transitionButton}
              style={{
                backgroundColor: '#524646',
                borderLeft: `10px solid ${trans.color}`,
                borderTop: '0',
                borderRight: '0',
                borderBottom: '0',
                color: '#fff'
              }}
              onClick={() => handleTransition(trans.transition)}
              loading={loading === trans.transition}
              disabled={loading !== null && loading !== trans.transition}
            >
              {trans.label}
            </Button>
          ))}
        </Space>
      </div>
    </Modal>
  )
}

const useStateChangeModalStyles = createStyles(({ css, token }) => ({
  content: css`
    padding: 16px 0;
  `,
  description: css`
    margin-bottom: 24px;
    color: ${token.colorText};
    font-size: 14px;
  `,
  buttonContainer: css`
    display: flex;
    gap: 8px;
  `,
  transitionButton: css`
    &:hover:not(:disabled) {
      opacity: 0.9 !important;
      background-color: #524646 !important;
    }

    &:disabled {
      opacity: 0.5 !important;
      background-color: #524646 !important;
    }

    &:focus {
      background-color: #524646 !important;
    }
  `
}))
