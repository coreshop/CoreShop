/**
 * CoreShop OrderBundle Info Tab
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
import { Card, Button, Space, Modal } from 'antd'
import { createStyles } from 'antd-style'
import { FolderOpenOutlined, ExclamationCircleOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import type { SaleTabProps } from '../registry'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { useDataObjectHelper } from "@pimcore/studio-ui-bundle/modules/data-object"
import { useSaleContext } from '../context/SaleActionsContext'

interface StateHistoryItem {
  title: string
  description: string
  date: string | number
}

export const InfoTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { sale, onReload } = useSaleContext()
  const { styles } = useInfoTabStyles()
  const { openDataObject } = useDataObjectHelper()
  const [loadingTransition, setLoadingTransition] = React.useState<string | null>(null)

  if (!sale) return null

  // Get states history from sale
  const statesHistory: StateHistoryItem[] = (sale as any).statesHistory || []
  const availableTransitions = (sale as any).availableOrderTransitions || []

  // Open Pimcore DataObject
  const handleOpenObject = () => {
    void openDataObject({ config: { id: sale.id } })
  }

  const getTransitionName = (transition: any): string => {
    if (typeof transition === 'object') {
      return transition.transition || transition.name || ''
    }

    return String(transition)
  }

  // Handle state transition
  const handleTransition = async (transition: any) => {
    const transitionName = getTransitionName(transition)
    const transitionLabel = transition.label || transitionName

    if (!transitionName) {
      return
    }

    // Show confirmation dialog for all transitions
    Modal.confirm({
      title: t('coreshop_confirm_transition', { defaultValue: 'Confirm Transition' }),
      icon: <ExclamationCircleOutlined />,
      content: t('coreshop_confirm_transition_content', {
        defaultValue: 'Are you sure you want to apply transition "{{transition}}"?',
        transition: transitionLabel
      }),
      okText: t('yes', { defaultValue: 'Yes' }),
      cancelText: t('no', { defaultValue: 'No' }),
      onOk: async () => {
        setLoadingTransition(transitionName)

        try {
          const payload = new URLSearchParams()
          payload.append('id', String(sale.id))
          payload.append('transition', transitionName)

          const response = await fetch('/pimcore-studio/api/coreshop/order/update-order-state', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: payload.toString()
          })

          const result = await response.json()

          if (result.success) {
            void messageApi.success(t('coreshop_change_state_success', { defaultValue: 'State changed successfully' }))
            onReload()
          } else {
            void messageApi.error(renderApiError(result.message || t('coreshop_change_state_error', { defaultValue: 'Failed to change state' })))
          }
        } catch (error) {
          void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_change_state_error', { defaultValue: 'Failed to change state' }))))
        } finally {
          setLoadingTransition(null)
        }
      }
    })
  }

  // Get transition display name
  const getTransitionLabel = (transition: any): string => {
    if (typeof transition === 'object') {
      return transition.label || transition.name || t('coreshop_transition', { defaultValue: 'Transition' })
    }
    return String(transition).charAt(0).toUpperCase() + String(transition).slice(1)
  }

  return (
    <Card
      title={t('coreshop_order_with_number', {
        defaultValue: 'Order: {{number}}',
        number: (sale as any).orderNumber || sale.id
      })}
      className={styles.card}
      extra={
        <Space size={6}>
          {/* Transition Buttons */}
          {availableTransitions.map((transition: any, index: number) => {
            const transitionName = getTransitionName(transition)
            const transitionColor = typeof transition === 'object' ? transition.color : undefined
            const isCancel = transitionName === 'cancel'

            return (
              <Button
                key={`${transitionName}-${index}`}
                size="small"
                className={isCancel ? styles.cancelButton : styles.transitionButton}
                loading={loadingTransition === transitionName}
                disabled={loadingTransition !== null && loadingTransition !== transitionName}
                style={
                  isCancel
                    ? undefined
                    : transitionColor
                      ? { borderColor: transitionColor, color: transitionColor }
                      : undefined
                }
                onClick={() => handleTransition(transition)}
              >
                {getTransitionLabel(transition)}
              </Button>
            )
          })}
          {/* Open DataObject Tool */}
          <Button
            type="text"
            icon={<FolderOpenOutlined />}
            onClick={handleOpenObject}
            size="small"
            title={t('coreshop_open_data_object', { defaultValue: 'Open DataObject' })}
          />
        </Space>
      }
    >
      {statesHistory.length === 0 ? (
        <div className={styles.emptyTimeline}>
          {t('coreshop_no_state_history', { defaultValue: 'No state history available' })}
        </div>
      ) : (
        <div className={styles.timelineContainer}>
          {statesHistory.map((item, index) => {
            const dateFormatted = typeof item.date === 'number'
              ? formatDateTime(item.date)
              : String(item.date)

            return (
              <div key={`${item.title}-${index}`} className={styles.timelineItem}>
                <div className={styles.timelineDot} />
                {index < statesHistory.length - 1 && <div className={styles.timelineLine} />}
                <div className={styles.timelineContent}>
                  <div className={styles.timelineHeader}>
                    <span className={styles.timelineTitle}>{item.title}</span>
                    <span className={styles.timelineDate}>{dateFormatted}</span>
                  </div>
                  {item.description && item.description !== item.title && (
                    <div className={styles.timelineDescription}>{item.description}</div>
                  )}
                </div>
              </div>
            )
          })}
        </div>
      )}
    </Card>
  )
}

const useInfoTabStyles = createStyles(({ css, token }) => ({
  card: css``,
  transitionButton: css`
    font-weight: 500;
    border-width: 1.5px;

    &:hover {
      opacity: 0.85;
    }
  `,
  cancelButton: css`
    color: ${token.colorError} !important;
    border-color: ${token.colorError} !important;
    font-weight: 500;

    &:hover {
      color: #fff !important;
      background-color: ${token.colorError} !important;
      border-color: ${token.colorError} !important;
    }
  `,
  emptyTimeline: css`
    padding: 24px;
    text-align: center;
    color: ${token.colorTextTertiary};
    font-size: 13px;
  `,
  timelineContainer: css`
    max-height: 280px;
    overflow-y: auto;
    padding-right: 4px;
  `,
  timelineItem: css`
    position: relative;
    padding-left: 24px;
    padding-bottom: 16px;

    &:last-child {
      padding-bottom: 0;
    }
  `,
  timelineDot: css`
    position: absolute;
    left: 0;
    top: 6px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: ${token.colorPrimary};
    z-index: 1;
  `,
  timelineLine: css`
    position: absolute;
    left: 3px;
    top: 18px;
    bottom: 0;
    width: 2px;
    background: ${token.colorBorderSecondary};
  `,
  timelineContent: css`
    display: flex;
    flex-direction: column;
    gap: 2px;
  `,
  timelineHeader: css`
    display: flex;
    align-items: baseline;
    gap: 8px;
  `,
  timelineTitle: css`
    font-size: 13px;
    font-weight: 500;
    color: ${token.colorText};
  `,
  timelineDescription: css`
    font-size: 12px;
    color: ${token.colorTextTertiary};
  `,
  timelineDate: css`
    font-size: 11px;
    color: ${token.colorTextTertiary};
    white-space: nowrap;
    margin-left: auto;
  `
}))
