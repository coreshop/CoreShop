/**
 * CoreShop OrderBundle Create Payment Modal
 *
 * Pattern from ExtJS: /order/createPayment.js
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect } from 'react'
import { Modal } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { SchemaForm } from '@coreshop/studio-form'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { toDayJs, fromDayJs } from '@pimcore/studio-ui-bundle/components'

export interface CreatePaymentModalProps {
  open: boolean
  orderId: number
  unpaidAmount?: number
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Create Payment Modal
 *
 * Allows adding a payment to an order
 */
export const CreatePaymentModal: React.FC<CreatePaymentModalProps> = ({
  open,
  orderId,
  unpaidAmount,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState<Record<string, unknown>>({})

  // Set default values when modal opens
  useEffect(() => {
    if (open) {
      setFormData({
        date: toDayJs(new Date().toISOString()),
        amount: unpaidAmount ? unpaidAmount / 100 : undefined
      })
    }
  }, [open, unpaidAmount])

  // Handle save
  const handleSave = async () => {
    try {
      setLoading(true)

      const payload = {
        id: orderId,
        date: fromDayJs(formData.date as any, 'dateString', 'YYYY-MM-DD'),
        paymentProvider: formData.paymentProvider,
        amount: formData.amount
      }

      const response = await fetch('/pimcore-studio/api/coreshop/order-payment/add-payment', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(payload as any)
      })

      const result = await response.json()

      if (result.success) {
        void messageApi.success(t('coreshop_payment_create_success', { defaultValue: 'Payment created successfully' }))
        setFormData({})
        onSuccess()
      } else {
        void messageApi.error(renderApiError(result.message || t('coreshop_payment_create_error', { defaultValue: 'Failed to create payment' })))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_payment_create_error', { defaultValue: 'Failed to create payment' }))))
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal
      title={t('coreshop_order_add_payment', { defaultValue: 'Add Payment' })}
      open={open}
      onCancel={onCancel}
      onOk={handleSave}
      okText={t('coreshop_save', { defaultValue: 'Save' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      confirmLoading={loading}
      destroyOnClose
    >
      <SchemaForm
        blockPrefix="coreshop_order_payment_creation"
        data={formData}
        onChange={(draft) => setFormData((prev) => ({ ...prev, ...draft }))}
      />
    </Modal>
  )
}
