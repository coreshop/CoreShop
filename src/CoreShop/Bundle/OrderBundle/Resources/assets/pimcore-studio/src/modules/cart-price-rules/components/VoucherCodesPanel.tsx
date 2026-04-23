/**
 * CoreShop OrderBundle Studio Plugin
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
import { Button, Table, Space, Popconfirm, Modal, Form, Input, InputNumber, Select, Tag } from 'antd'
import { PlusOutlined, DeleteOutlined, DownloadOutlined, ThunderboltOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { renderApiError } from '@coreshop/resource/src/entities'
import type { CartPriceRule, VoucherCode } from '../types'
import { cartPriceRuleApi } from '../api'

interface VoucherCodesPanelProps {
  rule: CartPriceRule
  disabled?: boolean
}

export const VoucherCodesPanel: React.FC<VoucherCodesPanelProps> = ({
  rule,
  disabled = false
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [vouchers, setVouchers] = React.useState<VoucherCode[]>([])
  const [loading, setLoading] = React.useState(false)
  const [total, setTotal] = React.useState(0)
  const [page, setPage] = React.useState(1)
  const [pageSize, setPageSize] = React.useState(20)
  const [createModalOpen, setCreateModalOpen] = React.useState(false)
  const [generateModalOpen, setGenerateModalOpen] = React.useState(false)
  const [createForm] = Form.useForm()
  const [generateForm] = Form.useForm()

  const loadVouchers = React.useCallback(() => {
    if (!rule.id) return

    setLoading(true)
    const start = (page - 1) * pageSize
    cartPriceRuleApi.getVoucherCodes(rule.id, { start, limit: pageSize })
      .then(({ data, total }) => {
        setVouchers(data)
        setTotal(total)
      })
      .catch(() => {
        void messageApi.error(renderApiError(t('coreshop_voucher_codes_load_error', { defaultValue: 'Failed to load voucher codes' })))
        setVouchers([])
      })
      .finally(() => setLoading(false))
  }, [rule.id, page, pageSize, t])

  React.useEffect(() => {
    if (!disabled) {
      loadVouchers()
    }
  }, [loadVouchers, disabled])

  const handleCreate = async () => {
    if (!rule.id) return

    try {
      const values = await createForm.validateFields()
      await cartPriceRuleApi.createVoucherCode(rule.id, values.code)
      void messageApi.success(t('coreshop_voucher_code_create_success', { defaultValue: 'Voucher code created' }))
      setCreateModalOpen(false)
      createForm.resetFields()
      loadVouchers()
    } catch (error: any) {
      if (error.errorFields) return // Validation error
      void messageApi.error(renderApiError(error.message || t('coreshop_voucher_code_create_error', { defaultValue: 'Failed to create voucher code' })))
    }
  }

  const handleGenerate = async () => {
    if (!rule.id) return

    try {
      const values = await generateForm.validateFields()
      await cartPriceRuleApi.generateVoucherCodes({
        cartPriceRule: rule.id,
        ...values
      })
      void messageApi.success(t('coreshop_voucher_codes_generate_success', { defaultValue: 'Voucher codes generated' }))
      setGenerateModalOpen(false)
      generateForm.resetFields()
      loadVouchers()
    } catch (error: any) {
      if (error.errorFields) return // Validation error
      void messageApi.error(renderApiError(error.message || t('coreshop_voucher_codes_generate_error', { defaultValue: 'Failed to generate voucher codes' })))
    }
  }

  const handleDelete = async (id: number) => {
    try {
      await cartPriceRuleApi.deleteVoucherCode(id)
      void messageApi.success(t('coreshop_voucher_code_delete_success', { defaultValue: 'Voucher code deleted' }))
      loadVouchers()
    } catch (error) {
      void messageApi.error(renderApiError(t('coreshop_voucher_code_delete_error', { defaultValue: 'Failed to delete voucher code' })))
    }
  }

  const handleExport = () => {
    if (!rule.id) return
    const start = (page - 1) * pageSize
    const url = cartPriceRuleApi.getVoucherCodesExportUrl(rule.id, { start, limit: pageSize })
    window.open(url, '_blank')
  }

  const columns = [
    {
      title: t('coreshop_cart_pricerule_voucher_code', { defaultValue: 'Code' }),
      dataIndex: 'code',
      key: 'code'
    },
    {
      title: t('coreshop_cart_pricerule_creation_date', { defaultValue: 'Creation Date' }),
      dataIndex: 'creationDate',
      key: 'creationDate',
      width: 180,
      render: (date: string) => date ? new Date(date).toLocaleString() : '-'
    },
    {
      title: t('coreshop_cart_pricerule_used', { defaultValue: 'Used' }),
      dataIndex: 'used',
      key: 'used',
      width: 100,
      render: (used: boolean) => (
        <Tag color={used ? 'red' : 'green'}>
          {used
            ? t('yes', { defaultValue: 'Yes' })
            : t('no', { defaultValue: 'No' })}
        </Tag>
      )
    },
    {
      title: t('coreshop_cart_pricerule_uses', { defaultValue: 'Uses' }),
      dataIndex: 'uses',
      key: 'uses',
      width: 80
    },
    {
      title: '',
      key: 'actions',
      width: 60,
      render: (_: any, record: VoucherCode) => (
        <Popconfirm
          title={t('coreshop_voucher_code_delete_confirm', { defaultValue: 'Delete voucher code?' })}
          onConfirm={() => handleDelete(record.id)}
          okText={t('yes', { defaultValue: 'Yes' })}
          cancelText={t('no', { defaultValue: 'No' })}
          disabled={record.used}
        >
          <Button
            type="text"
            size="small"
            icon={<DeleteOutlined />}
            danger
            disabled={record.used}
          />
        </Popconfirm>
      )
    }
  ]

  return (
    <div style={{ padding: 24 }}>
      <Space direction="vertical" size="large" style={{ width: '100%' }}>
        <Space>
          <Button
            icon={<PlusOutlined />}
            onClick={() => setCreateModalOpen(true)}
            disabled={disabled}
          >
            {t('coreshop_cart_pricerule_create_voucher', { defaultValue: 'Create Voucher Code' })}
          </Button>
          <Button
            icon={<ThunderboltOutlined />}
            onClick={() => setGenerateModalOpen(true)}
            disabled={disabled}
          >
            {t('coreshop_cart_pricerule_generate_vouchers', { defaultValue: 'Generate Voucher Codes' })}
          </Button>
          <Button
            icon={<DownloadOutlined />}
            onClick={handleExport}
            disabled={disabled || vouchers.length === 0}
          >
            {t('coreshop_cart_pricerule_vouchers_export', { defaultValue: 'Export Voucher Codes (CSV)' })}
          </Button>
        </Space>

        <Table
          columns={columns}
          dataSource={vouchers}
          loading={loading}
          rowKey="id"
          pagination={{
            current: page,
            pageSize,
            total,
            onChange: (newPage, newPageSize) => {
              setPage(newPage)
              if (newPageSize !== pageSize) {
                setPageSize(newPageSize)
              }
            }
          }}
        />
      </Space>

      {/* Create Modal */}
      <Modal
        title={t('coreshop_cart_pricerule_create_voucher', { defaultValue: 'Create Voucher Code' })}
        open={createModalOpen}
        onOk={handleCreate}
        onCancel={() => {
          setCreateModalOpen(false)
          createForm.resetFields()
        }}
        okText={t('coreshop_create', { defaultValue: 'Create' })}
        cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      >
        <Form form={createForm} layout="vertical">
          <Form.Item
            name="code"
            label={t('coreshop_cart_pricerule_voucher_code', { defaultValue: 'Code' })}
            rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
          >
            <Input placeholder={t('coreshop_cart_pricerule_voucher_code', { defaultValue: 'Code' })} />
          </Form.Item>
        </Form>
      </Modal>

      {/* Generate Modal */}
      <Modal
        title={t('coreshop_cart_pricerule_generate_vouchers', { defaultValue: 'Generate Voucher Codes' })}
        open={generateModalOpen}
        onOk={handleGenerate}
        onCancel={() => {
          setGenerateModalOpen(false)
          generateForm.resetFields()
        }}
        okText={t('coreshop_generate', { defaultValue: 'Generate' })}
        cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      >
        <Form
          form={generateForm}
          layout="vertical"
          initialValues={{
            format: 'alphanumeric'
          }}
        >
          <Form.Item
            name="amount"
            label={t('coreshop_cart_pricerule_amount', { defaultValue: 'Amount' })}
            rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
          >
            <InputNumber min={1} style={{ width: '100%' }} placeholder={t('coreshop_cart_pricerule_amount', { defaultValue: 'Amount' })} />
          </Form.Item>

          <Form.Item
            name="length"
            label={t('coreshop_cart_pricerule_length', { defaultValue: 'Length' })}
            rules={[{ required: true, message: t('coreshop_required', { defaultValue: 'Required' }) }]}
          >
            <InputNumber min={1} style={{ width: '100%' }} placeholder={t('coreshop_cart_pricerule_length', { defaultValue: 'Length' })} />
          </Form.Item>

          <Form.Item
            name="format"
            label={t('coreshop_cart_pricerule_format', { defaultValue: 'Format' })}
            rules={[{ required: true }]}
          >
            <Select>
              <Select.Option value="alphanumeric">{t('coreshop_cart_pricerule_alphanumeric', { defaultValue: 'Alphanumeric' })}</Select.Option>
              <Select.Option value="alphabetic">{t('coreshop_cart_pricerule_alphabetic', { defaultValue: 'Alphabetic' })}</Select.Option>
              <Select.Option value="numeric">{t('coreshop_cart_pricerule_numeric', { defaultValue: 'Numeric' })}</Select.Option>
            </Select>
          </Form.Item>

          <Form.Item name="prefix" label={t('coreshop_cart_pricerule_prefix', { defaultValue: 'Prefix' })}>
            <Input placeholder={t('coreshop_cart_pricerule_prefix', { defaultValue: 'Prefix' })} />
          </Form.Item>

          <Form.Item name="suffix" label={t('coreshop_cart_pricerule_suffix', { defaultValue: 'Suffix' })}>
            <Input placeholder={t('coreshop_cart_pricerule_suffix', { defaultValue: 'Suffix' })} />
          </Form.Item>

          <Form.Item name="hyphensOn" label={t('coreshop_cart_pricerule_hyphensOn', { defaultValue: 'Hyphens all X characters' })}>
            <InputNumber min={0} style={{ width: '100%' }} placeholder={t('coreshop_cart_pricerule_hyphensOn', { defaultValue: 'Hyphens all X characters' })} />
          </Form.Item>
        </Form>
      </Modal>
    </div>
  )
}
