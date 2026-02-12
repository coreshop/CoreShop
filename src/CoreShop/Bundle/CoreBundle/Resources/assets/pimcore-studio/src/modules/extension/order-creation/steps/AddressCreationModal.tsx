/**
 * CoreShop CoreBundle - Address Creation Modal
 *
 * Modal for creating a new address inline during order creation.
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
import { Modal, Form, Input, Row, Col, Alert, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import { loadCountries } from '@coreshop/address/src/components/CountrySelect'

// Module-level cache for states
let cachedStates: Array<{ value: number; label: string }> | null = null
let stateLoadPromise: Promise<Array<{ value: number; label: string }>> | null = null

const loadStates = async (): Promise<Array<{ value: number; label: string }>> => {
  if (cachedStates) return cachedStates
  if (stateLoadPromise) return stateLoadPromise

  const promise = (async () => {
    try {
      const res = await fetch('/pimcore-studio/api/coreshop/states/list', {
        credentials: 'same-origin'
      })
      const data = await res.json()
      const states = data.data || data || []
      const result = states.map((s: { id: number; name?: string; countryName?: string }) => ({
        value: s.id,
        label: s.countryName ? `${s.name} (${s.countryName})` : (s.name ?? `#${s.id}`)
      }))
      cachedStates = result
      return result
    } catch (err) {
      console.error('Failed to load states:', err)
      return []
    } finally {
      stateLoadPromise = null
    }
  })()

  stateLoadPromise = promise
  return promise
}

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
  onCreated
}) => {
  const { t } = useTranslation()
  const [form] = Form.useForm()
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const [countryOptions, setCountryOptions] = useState<Array<{ value: number; label: string }>>([])
  const [countryLoading, setCountryLoading] = useState(true)
  const [stateOptions, setStateOptions] = useState<Array<{ value: number; label: string }>>([])
  const [stateLoading, setStateLoading] = useState(true)

  React.useEffect(() => {
    if (!open) return

    void (async () => {
      setCountryLoading(true)
      try {
        const opts = await loadCountries()
        setCountryOptions(opts)
      } finally {
        setCountryLoading(false)
      }
    })()

    void (async () => {
      setStateLoading(true)
      try {
        const opts = await loadStates()
        setStateOptions(opts)
      } finally {
        setStateLoading(false)
      }
    })()
  }, [open])

  const handleSubmit = async (): Promise<void> => {
    try {
      const values = await form.validateFields()
      setSubmitting(true)
      setError(null)

      const params = new URLSearchParams()
      params.append('customer', String(customerId))
      params.append('address[firstname]', values.firstname)
      params.append('address[lastname]', values.lastname)
      params.append('address[street]', values.street)
      params.append('address[number]', values.number || '')
      params.append('address[postcode]', values.postcode)
      params.append('address[city]', values.city)
      params.append('address[country]', String(values.country))

      if (values.company) {
        params.append('address[company]', values.company)
      }
      if (values.salutation) {
        params.append('address[salutation]', values.salutation)
      }
      if (values.state) {
        params.append('address[state]', String(values.state))
      }
      if (values.phoneNumber) {
        params.append('address[phoneNumber]', values.phoneNumber)
      }

      const res = await fetch('/admin/coreshop/order/address/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'same-origin',
        body: params
      })

      const data = await res.json()

      if (data.success && data.id) {
        form.resetFields()
        onCreated(data.id)
      } else {
        const message = typeof data.message === 'string'
          ? data.message
          : data.message
            ? JSON.stringify(data.message)
            : 'Failed to create address'
        setError(message)
      }
    } catch (err) {
      if (err && typeof err === 'object' && 'errorFields' in err) {
        // Form validation error, handled by antd
        return
      }
      setError(err instanceof Error ? err.message : 'Failed to create address')
    } finally {
      setSubmitting(false)
    }
  }

  const handleCancel = (): void => {
    form.resetFields()
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

      <Form form={form} layout="vertical">
        <Row gutter={16}>
          <Col span={12}>
            <Form.Item
              name="firstname"
              label={t('coreshop_address_create_firstname', { defaultValue: 'Firstname' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="lastname"
              label={t('coreshop_address_create_lastname', { defaultValue: 'Lastname' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Form.Item
          name="company"
          label={t('coreshop_company', { defaultValue: 'Company' })}
        >
          <Input />
        </Form.Item>

        <Row gutter={16}>
          <Col span={16}>
            <Form.Item
              name="street"
              label={t('coreshop_address_create_street', { defaultValue: 'Street' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={8}>
            <Form.Item
              name="number"
              label={t('coreshop_address_create_number', { defaultValue: 'Number' })}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={16}>
          <Col span={8}>
            <Form.Item
              name="postcode"
              label={t('coreshop_address_create_postcode', { defaultValue: 'Post Code' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={16}>
            <Form.Item
              name="city"
              label={t('coreshop_address_create_city', { defaultValue: 'City' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={16}>
          <Col span={12}>
            <Form.Item
              name="country"
              label={t('coreshop_address_create_country', { defaultValue: 'Country' })}
              rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
            >
              <Select
                loading={countryLoading}
                options={countryOptions}
                placeholder={t('coreshop_select_country', { defaultValue: 'Select Country' })}
                showSearch
                optionFilterProp="label"
                style={{ width: '100%' }}
              />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="state"
              label={t('coreshop_state', { defaultValue: 'State' })}
            >
              <Select
                loading={stateLoading}
                options={stateOptions}
                placeholder={t('coreshop_select_state', { defaultValue: 'Select State' })}
                showSearch
                optionFilterProp="label"
                allowClear
                style={{ width: '100%' }}
              />
            </Form.Item>
          </Col>
        </Row>

        <Form.Item
          name="phoneNumber"
          label={t('coreshop_address_create_phone_number', { defaultValue: 'Phone Number' })}
        >
          <Input />
        </Form.Item>
      </Form>
    </Modal>
  )
}
