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
import { Card, Table, Button, Space, Modal } from 'antd'
import { createStyles } from 'antd-style'
import { FolderOpenOutlined, ExclamationCircleOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { SaleTabProps } from '../registry'
import type { ColumnsType } from 'antd/es/table'
import { useDataObjectHelper } from "@pimcore/studio-ui-bundle/modules/data-object"
import { useSaleContext } from '../context/SaleActionsContext'

interface StateHistoryItem {
  title: string
  description: string
  date: string | number
}

export const InfoTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const { sale } = useSaleContext()
  const { styles } = useInfoTabStyles()
  const { openDataObject } = useDataObjectHelper()

  if (!sale) return null

  // Get states history from sale
  const statesHistory: StateHistoryItem[] = (sale as any).statesHistory || []
  const availableTransitions = (sale as any).availableOrderTransitions || []

  // Open Pimcore DataObject
  const handleOpenObject = () => {
    void openDataObject({ config: { id: sale.id } })
  }

  // Handle state transition
  const handleTransition = async (transition: any) => {
    const transitionName = transition.transition
    const transitionLabel = transition.label || transitionName

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
        // TODO: Implement state transition API call
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

  // Table columns
  const columns: ColumnsType<StateHistoryItem> = [
    {
      title: t('coreshop_order_state', { defaultValue: 'Order State' }),
      dataIndex: 'title',
      key: 'title',
      width: '50%'
    },
    {
      title: t('coreshop_date', { defaultValue: 'Date' }),
      dataIndex: 'date',
      key: 'date',
      width: '50%',
    }
  ]

  return (
    <Card
      title={t('coreshop_order_with_number', {
        defaultValue: 'Order: {{number}}',
        number: (sale as any).orderNumber || sale.id
      })}
      className={styles.card}
      extra={
        <Space>
          {/* Transition Buttons */}
          {availableTransitions.map((transition: any, index: number) => {
            const transitionName = transition.transition
            const transitionColor = typeof transition === 'object' ? transition.color : undefined
            const isCancel = transitionName === 'cancel'

            // Cancel button: red background (#d83a3a)
            // Other transitions: dark background (#524646) with colored left border
            const buttonStyle = isCancel
              ? {
                  backgroundColor: '#d83a3a',
                  borderColor: '#d83a3a',
                  color: '#fff'
                }
              : transitionColor
                ? {
                    backgroundColor: '#524646',
                    borderLeft: `10px solid ${transitionColor}`,
                    color: '#fff'
                  }
                : undefined

            return (
              <Button
                key={`${transitionName}-${index}`}
                type="primary"
                style={buttonStyle}
                className={isCancel ? styles.cancelButton : undefined}
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
            title={t('coreshop_open_data_object', { defaultValue: 'Open DataObject' })}
          />
        </Space>
      }
    >
      <Table
        columns={columns}
        dataSource={statesHistory}
        rowKey={(record, index) => `${record.title}-${index}`}
        pagination={false}
        size="small"
        expandable={{
          expandedRowRender: (record) => (
            <div className={styles.expandedRow}>
              {record.description || '-'}
            </div>
          ),
          rowExpandable: (record) => !!record.description
        }}
      />
    </Card>
  )
}

const useInfoTabStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-head {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }

    .ant-card-body {
      padding: 0;
    }

    .ant-table {
      .ant-table-thead > tr > th {
        background: ${token.colorBgContainer};
        font-weight: 600;
      }
    }
  `,
  expandedRow: css`
    padding: 12px 24px;
    background: ${token.colorBgLayout};
    color: ${token.colorTextSecondary};
    font-size: 13px;
  `,
  cancelButton: css`
    &:hover {
      background-color: #c72a2a !important;
      border-color: #c72a2a !important;
    }
  `
}))
