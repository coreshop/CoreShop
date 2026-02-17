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

import React from 'react'
import { Modal, Form, DatePicker, InputNumber } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { createStyles } from 'antd-style'
import dayjs, { type Dayjs } from 'dayjs'
import { useTranslation } from 'react-i18next'
import { PaymentProviderSelect } from '@coreshop/payment/src/components'
import { getErrorMessage } from '@coreshop/resource/src/entities'

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
  const { styles } = useCreatePaymentModalStyles()
  const messageApi = useMessage()
  const [form] = Form.useForm()
  const [loading, setLoading] = React.useState(false)

  // Set default values when modal opens
  React.useEffect(() => {
    if (open) {
      form.setFieldsValue({
        date: dayjs(),
        // Set unpaid amount if available (convert from cents to currency)
        amount: unpaidAmount ? unpaidAmount / 100 : undefined
      })
    }
  }, [open, form, unpaidAmount])

  // Handle save
  const handleSave = async () => {
    try {
      const values = await form.validateFields()
      setLoading(true)

      // Format date to ISO 8601 format with timezone
      const date = values.date as Dayjs
      const formattedDate = date.format('YYYY-MM-DDTHH:mm:ss')

      const payload = {
        id: orderId,
        date: formattedDate,
        paymentProvider: values.paymentProvider,
        amount: values.amount
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
        form.resetFields()
        onSuccess()
      } else {
        void messageApi.error(result.message || t('coreshop_payment_create_error', { defaultValue: 'Failed to create payment' }))
      }
    } catch (error) {
      if (error instanceof Error && error.message !== 'Validation failed') {
        void messageApi.error(getErrorMessage(error, t('coreshop_payment_create_error', { defaultValue: 'Failed to create payment' })))
      }
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
      className={styles.modal}
    >
      <Form
        form={form}
        layout="vertical"
        className={styles.form}
      >
        <Form.Item
          label={t('coreshop_date', { defaultValue: 'Date' })}
          name="date"
          rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
        >
          <DatePicker
            style={{ width: '100%' }}
            format="YYYY-MM-DD"
          />
        </Form.Item>

        <Form.Item
          label={t('coreshop_paymentProvider', { defaultValue: 'Payment Provider' })}
          name="paymentProvider"
          rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
        >
          <PaymentProviderSelect placeholder={t('coreshop_select_payment_provider', { defaultValue: 'Select Payment Provider' })} />
        </Form.Item>

        <Form.Item
          label={t('coreshop_amount', { defaultValue: 'Amount' })}
          name="amount"
          rules={[
            { required: true, message: t('coreshop_required', { defaultValue: 'Required' }) },
            { type: 'number', min: 0, message: t('coreshop_amount_must_be_positive', { defaultValue: 'Amount must be positive' }) }
          ]}
        >
          <InputNumber
            style={{ width: '100%' }}
            precision={2}
            min={0}
            placeholder="0.00"
          />
        </Form.Item>
      </Form>
    </Modal>
  )
}

const useCreatePaymentModalStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal-body {
      padding-top: 24px;
    }
  `,
  form: css`
    .ant-form-item-label > label.ant-form-item-required:not(.ant-form-item-required-mark-optional)::before {
      color: ${token.colorError};
    }
  `
}))
