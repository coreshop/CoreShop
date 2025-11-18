/**
 * CoreShop OrderBundle - Create Payment Button
 */

import React from 'react'
import { Button } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { useSaleContext } from '../context/SaleActionsContext'

export const CreatePaymentButton: React.FC = () => {
  const { t } = useTranslation()
  const { sale, openAction } = useSaleContext()

  // Don't show button if payment creation is not allowed
  const saleData = sale as any
  if (!saleData?.paymentCreationAllowed) {
    return null
  }

  return (
    <Button
      type="default"
      icon={<PlusOutlined />}
      onClick={() => openAction('createPayment')}
    >
      {t('coreshop_create_payment', { defaultValue: 'Create Payment' })}
    </Button>
  )
}
