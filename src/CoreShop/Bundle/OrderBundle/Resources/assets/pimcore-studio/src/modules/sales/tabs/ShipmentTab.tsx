/**
 * CoreShop OrderBundle Shipment Tab
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
import { Table, Button, Card, Empty } from 'antd'
import { createStyles } from 'antd-style'
import { FolderOpenOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { formatDateTime } from '@coreshop/pimcore/src/utils'
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps, SaleTab } from '../registry'
import { StateChangeModal, ShipmentDetailModal, CreateShipmentModal as BaseCreateShipmentModal, CreateShipmentButton } from '../components'
import { getComponent } from '../registry'
import { useSaleContext } from '../context/SaleActionsContext'

interface StateInfo {
  label: string
  state: string
  color: string
}

interface Transition {
  label: string
  transition: string
  color: string
}

interface ShipmentItem {
  _itemName: string
  quantity: number
}

interface Shipment {
  id: number
  shipmentDate: number
  shipmentNumber: string
  carrierName: string
  trackingCode?: string
  weight?: number
  stateInfo: StateInfo
  transitions: Transition[]
  items: ShipmentItem[]
}

export const ShipmentTab: React.FC<SaleTabProps> = () => {
  const { t } = useTranslation()
  const { sale, onReload, isActionOpen, openAction, closeAction, buttonRegistry } = useSaleContext()
  const { styles } = useShipmentTabStyles()
  const [stateChangeShipment, setStateChangeShipment] = React.useState<Shipment | null>(null)
  const [detailShipment, setDetailShipment] = React.useState<Shipment | null>(null)

  if (!sale) return null

  // Get CreateShipmentModal from registry (CoreBundle may have registered an extended version)
  const CreateShipmentModal = getComponent('CreateShipmentModal', BaseCreateShipmentModal)

  const shipments = ((sale as any).shipments || []) as Shipment[]

  // Register button in toolbar
  React.useEffect(() => {
    if ((sale as any)?.shipmentCreationAllowed) {
      buttonRegistry.add('createShipment', CreateShipmentButton, 20)
      return () => buttonRegistry.remove('createShipment')
    }
  }, [buttonRegistry, sale])

  const columns: Array<ColumnType<Shipment>> = [
    {
      title: t('coreshop_date', { defaultValue: 'Date' }),
      dataIndex: 'shipmentDate',
      key: 'shipmentDate',
      width: 160,
      render: (date) => formatDateTime(date)
    },
    {
      title: t('coreshop_carrier', { defaultValue: 'Carrier' }),
      dataIndex: 'carrierName',
      key: 'carrierName',
      width: 140,
      render: (carrier) => carrier || '–'
    },
    {
      title: t('coreshop_tracking_code', { defaultValue: 'Tracking' }),
      dataIndex: 'trackingCode',
      key: 'trackingCode',
      ellipsis: true,
      render: (code) => code || '–'
    },
    {
      title: t('coreshop_status', { defaultValue: 'Status' }),
      key: 'state',
      width: 120,
      align: 'center',
      render: (_, record) => {
        const hasTransitions = record.transitions && record.transitions.length > 0
        return (
          <Button
            style={{
              backgroundColor: record.stateInfo.color,
              borderColor: record.stateInfo.color,
              color: '#fff',
              cursor: hasTransitions ? 'pointer' : 'default',
              minWidth: 90
            }}
            size="small"
            onClick={() => {
              if (hasTransitions) {
                setStateChangeShipment(record)
              }
            }}
            disabled={!hasTransitions}
          >
            {record.stateInfo.label}
          </Button>
        )
      }
    },
    {
      title: '',
      key: 'actions',
      width: 50,
      align: 'center',
      render: (_, record) => (
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          size="small"
          title={t('coreshop_open_shipment_details', { defaultValue: 'Open Shipment Details' })}
          onClick={() => setDetailShipment(record)}
        />
      )
    }
  ]

  return (
    <>
      <Card
        title={t('coreshop_shipments', { defaultValue: 'Shipments' })}
        className={styles.card}
        extra={
          (sale as any).shipmentCreationAllowed && (
            <Button
              type="text"
              icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
              title={t('coreshop_order_add_shipment', { defaultValue: 'Add Shipment' })}
              onClick={() => openAction('createShipment')}
            />
          )
        }
      >
        {shipments.length === 0 ? (
          <Empty description={t('coreshop_no_shipments', { defaultValue: 'No shipments recorded' })} image={Empty.PRESENTED_IMAGE_SIMPLE} />
        ) : (
          <Table
            dataSource={shipments}
            columns={columns}
            rowKey="id"
            pagination={false}
            className={styles.table}
            size="small"
          />
        )}
      </Card>

      {/* State Change Modal */}
      {stateChangeShipment && (
        <StateChangeModal
          open={true}
          title={t('coreshop_change_shipment_state', { defaultValue: 'Change Shipment State' })}
          description={t('coreshop_change_shipment_state_description', { defaultValue: 'Select a transition to apply to this shipment' })}
          transitions={stateChangeShipment.transitions}
          url="/pimcore-studio/api/coreshop/order-shipment/update-shipment-state"
          id={stateChangeShipment.id}
          onSuccess={() => {
            setStateChangeShipment(null)
            onReload()
          }}
          onCancel={() => setStateChangeShipment(null)}
        />
      )}

      {/* Shipment Detail Modal */}
      {detailShipment && (
        <ShipmentDetailModal
          open={true}
          shipment={detailShipment}
          onClose={() => setDetailShipment(null)}
        />
      )}

      {/* Create Shipment Modal */}
      {isActionOpen('createShipment') && (
        <CreateShipmentModal
          open={true}
          orderId={(sale as any).id}
          currencyCode={
            typeof sale.currency === 'object' && sale.currency?.isoCode
              ? sale.currency.isoCode
              : typeof sale.currency === 'string'
                ? sale.currency
                : 'EUR'
          }
          carrierId={(sale as any).carrier}
          onSuccess={() => {
            closeAction('createShipment')
            onReload()
          }}
          onCancel={() => closeAction('createShipment')}
        />
      )}
    </>
  )
}

const useShipmentTabStyles = createStyles(({ css, token }) => ({
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
  `
}))
