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
import type { ColumnType } from 'antd/es/table'
import type { SaleTabProps, SaleTab } from '../registry'
import { StateChangeModal, ShipmentDetailModal, CreateShipmentModal as BaseCreateShipmentModal } from '../components'
import { getComponent } from '../registry'
import { shipmentEvents, SHIPMENT_EVENTS } from '../events/ShipmentEvents'

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

export const ShipmentTab: React.FC<SaleTabProps> = ({ sale, onReload }) => {
  const { styles } = useShipmentTabStyles()
  const [stateChangeShipment, setStateChangeShipment] = React.useState<Shipment | null>(null)
  const [detailShipment, setDetailShipment] = React.useState<Shipment | null>(null)
  const [createShipmentOpen, setCreateShipmentOpen] = React.useState(false)

  // Get CreateShipmentModal from registry (CoreBundle may have registered an extended version)
  const CreateShipmentModal = getComponent('CreateShipmentModal', BaseCreateShipmentModal)

  const shipments = ((sale as any).shipments || []) as Shipment[]

  // Listen for create shipment events from toolbar
  React.useEffect(() => {
    const handleCreateShipment = () => {
      setCreateShipmentOpen(true)
    }

    shipmentEvents.on(SHIPMENT_EVENTS.CREATE_SHIPMENT, handleCreateShipment)

    return () => {
      shipmentEvents.off(SHIPMENT_EVENTS.CREATE_SHIPMENT, handleCreateShipment)
    }
  }, [])

  // Format date
  const formatDate = (date?: number) => {
    if (!date) return '-'
    return new Date(date * 1000).toLocaleString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  }

  const columns: Array<ColumnType<Shipment>> = [
    {
      title: 'Date',
      dataIndex: 'shipmentDate',
      key: 'shipmentDate',
      width: 180,
      render: (date) => formatDate(date)
    },
    {
      title: 'Carrier',
      dataIndex: 'carrierName',
      key: 'carrierName',
      width: 150,
      render: (carrier) => carrier || '-'
    },
    {
      title: 'Tracking-Number',
      dataIndex: 'trackingCode',
      key: 'trackingCode',
      width: 200,
      render: (code) => code || '-'
    },
    {
      title: '',
      key: 'state',
      width: 150,
      render: (_, record) => {
        const hasTransitions = record.transitions && record.transitions.length > 0
        return (
          <Button
            style={{
              backgroundColor: record.stateInfo.color,
              borderColor: record.stateInfo.color,
              color: '#fff',
              cursor: hasTransitions ? 'pointer' : 'default'
            }}
            size="small"
            icon={hasTransitions ? <PlusOutlined style={{ fontSize: 10 }} /> : undefined}
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
      width: 80,
      render: (_, record) => (
        <Button
          type="text"
          icon={<FolderOpenOutlined />}
          size="small"
          title="Open Shipment Details"
          onClick={() => setDetailShipment(record)}
        />
      )
    }
  ]

  return (
    <>
      <Card
        title="Shipments"
        className={styles.card}
        extra={
          (sale as any).shipmentCreationAllowed && (
            <Button
              type="text"
              icon={<PlusOutlined style={{ color: '#52c41a', fontSize: 20 }} />}
              title="Add Shipment"
              onClick={() => setCreateShipmentOpen(true)}
            />
          )
        }
      >
        {shipments.length === 0 ? (
          <Empty description="No shipments recorded" image={Empty.PRESENTED_IMAGE_SIMPLE} />
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
          title="Change Shipment State"
          description="Select a transition to apply to this shipment"
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
      {createShipmentOpen && (
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
            setCreateShipmentOpen(false)
            onReload()
          }}
          onCancel={() => setCreateShipmentOpen(false)}
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
