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
        void messageApi.success('Payment created successfully')
        form.resetFields()
        onSuccess()
      } else {
        void messageApi.error(result.message || 'Failed to create payment')
      }
    } catch (error) {
      if (error instanceof Error && error.message !== 'Validation failed') {
        void messageApi.error(getErrorMessage(error, 'Failed to create payment'))
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal
      title="Add Payment"
      open={open}
      onCancel={onCancel}
      onOk={handleSave}
      okText="Save"
      cancelText="Cancel"
      confirmLoading={loading}
      className={styles.modal}
    >
      <Form
        form={form}
        layout="vertical"
        className={styles.form}
      >
        <Form.Item
          label="Date"
          name="date"
          rules={[{ required: true, message: 'Please select a date' }]}
        >
          <DatePicker
            style={{ width: '100%' }}
            format="YYYY-MM-DD"
          />
        </Form.Item>

        <Form.Item
          label="Payment Provider"
          name="paymentProvider"
          rules={[{ required: true, message: 'Please select a payment provider' }]}
        >
          <PaymentProviderSelect placeholder="Select a payment provider" />
        </Form.Item>

        <Form.Item
          label="Amount"
          name="amount"
          rules={[
            { required: true, message: 'Please enter an amount' },
            { type: 'number', min: 0, message: 'Amount must be positive' }
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
