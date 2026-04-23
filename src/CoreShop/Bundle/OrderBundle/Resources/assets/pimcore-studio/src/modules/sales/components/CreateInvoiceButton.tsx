/**
 * CoreShop OrderBundle - Create Invoice Button
 */

import React from 'react'
import { Button } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { useSaleContext } from '../context/SaleActionsContext'

export const CreateInvoiceButton: React.FC = () => {
  const { t } = useTranslation()
  const { sale, openAction } = useSaleContext()

  // Don't show button if invoice creation is not allowed
  const saleData = sale as any
  if (!saleData?.invoiceCreationAllowed) {
    return null
  }

  return (
    <Button
      type="default"
      icon={<PlusOutlined />}
      onClick={() => openAction('createInvoice')}
    >
      {t('coreshop_create_invoice', { defaultValue: 'Create Invoice' })}
    </Button>
  )
}
