/**
 * CoreShop OrderBundle - Create Shipment Button
 */

import React from 'react'
import { Button } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { useSaleContext } from '../context/SaleActionsContext'

export const CreateShipmentButton: React.FC = () => {
  const { t } = useTranslation()
  const { sale, openAction } = useSaleContext()

  // Don't show button if shipment creation is not allowed
  const saleData = sale as any
  if (!saleData?.shipmentCreationAllowed) {
    return null
  }

  return (
    <Button
      type="default"
      icon={<PlusOutlined />}
      onClick={() => openAction('createShipment')}
    >
      {t('coreshop_create_shipment', { defaultValue: 'Create Shipment' })}
    </Button>
  )
}
