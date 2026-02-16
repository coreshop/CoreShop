/**
 * CoreShop CoreBundle - Address Creation Modal
 *
 * Schema-driven modal for creating a new address inline during order creation.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState } from 'react'
import { Modal, Alert, Form } from 'antd'
import { useTranslation } from 'react-i18next'
import { SchemaForm } from '@coreshop/studio-form'

interface AddressCreationModalProps {
  open: boolean
  customerId: number
  onClose: () => void
  onCreated: (addressId: number) => void
}

export const AddressCreationModal: React.FC<AddressCreationModalProps> = ({
  open,
  customerId,
  onClose,
  onCreated,
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()
  const [formData, setFormData] = useState<Record<string, unknown>>({})
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const handleSubmit = async (): Promise<void> => {
    try {
      await form.validateFields()
      setSubmitting(true)
      setError(null)

      const address = (formData.address ?? {}) as Record<string, unknown>
      const params = new URLSearchParams()
      params.append('customer', String(customerId))

      const fieldMap: Record<string, string> = {
        firstname: 'address[firstname]',
        lastname: 'address[lastname]',
        street: 'address[street]',
        number: 'address[number]',
        postcode: 'address[postcode]',
        city: 'address[city]',
        country: 'address[country]',
        company: 'address[company]',
        salutation: 'address[salutation]',
        state: 'address[state]',
        phoneNumber: 'address[phoneNumber]',
      }

      for (const [field, paramName] of Object.entries(fieldMap)) {
        const value = address[field]
        if (value !== undefined && value !== null && value !== '') {
          params.append(paramName, String(value))
        }
      }

      const res = await fetch('/pimcore-studio/api/coreshop/order/address/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        credentials: 'same-origin',
        body: params,
      })

      const data = await res.json()

      if (data.success && data.id) {
        form.resetFields()
        setFormData({})
        onCreated(data.id)
      } else {
        const message =
          typeof data.message === 'string'
            ? data.message
            : data.message
              ? JSON.stringify(data.message)
              : 'Failed to create address'
        setError(message)
      }
    } catch (err) {
      if (err && typeof err === 'object' && 'errorFields' in err) {
        return
      }
      setError(err instanceof Error ? err.message : 'Failed to create address')
    } finally {
      setSubmitting(false)
    }
  }

  const handleCancel = (): void => {
    form.resetFields()
    setFormData({})
    setError(null)
    onClose()
  }

  return (
    <Modal
      title={t('coreshop_address_create', { defaultValue: 'Create Address' })}
      open={open}
      onOk={() => void handleSubmit()}
      onCancel={handleCancel}
      confirmLoading={submitting}
      okText={t('coreshop_create', { defaultValue: 'Create' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      width={600}
      destroyOnClose
    >
      {error && (
        <Alert
          type="error"
          message={error}
          showIcon
          closable
          onClose={() => setError(null)}
          style={{ marginBottom: 16 }}
        />
      )}

      <SchemaForm
        blockPrefix="coreshop_admin_address_creation"
        data={formData}
        onChange={(draft) => setFormData((prev) => ({ ...prev, ...draft }))}
        form={form}
      />
    </Modal>
  )
}
