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
import { Button, Table, Space, Popconfirm, message, Modal, Form, Input, InputNumber, Select, Tag } from 'antd'
import { PlusOutlined, DeleteOutlined, DownloadOutlined, ThunderboltOutlined } from '@ant-design/icons'
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
        message.error('Failed to load voucher codes')
        setVouchers([])
      })
      .finally(() => setLoading(false))
  }, [rule.id, page, pageSize])

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
      message.success('Voucher code created')
      setCreateModalOpen(false)
      createForm.resetFields()
      loadVouchers()
    } catch (error: any) {
      if (error.errorFields) return // Validation error
      message.error(error.message || 'Failed to create voucher code')
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
      message.success('Voucher codes generated')
      setGenerateModalOpen(false)
      generateForm.resetFields()
      loadVouchers()
    } catch (error: any) {
      if (error.errorFields) return // Validation error
      message.error(error.message || 'Failed to generate voucher codes')
    }
  }

  const handleDelete = async (id: number) => {
    try {
      await cartPriceRuleApi.deleteVoucherCode(id)
      message.success('Voucher code deleted')
      loadVouchers()
    } catch (error) {
      message.error('Failed to delete voucher code')
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
      title: 'Code',
      dataIndex: 'code',
      key: 'code'
    },
    {
      title: 'Creation Date',
      dataIndex: 'creationDate',
      key: 'creationDate',
      width: 180,
      render: (date: string) => date ? new Date(date).toLocaleString() : '-'
    },
    {
      title: 'Used',
      dataIndex: 'used',
      key: 'used',
      width: 100,
      render: (used: boolean) => (
        <Tag color={used ? 'red' : 'green'}>
          {used ? 'Yes' : 'No'}
        </Tag>
      )
    },
    {
      title: 'Uses',
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
          title="Delete voucher code?"
          onConfirm={() => handleDelete(record.id)}
          okText="Yes"
          cancelText="No"
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
            Create Voucher
          </Button>
          <Button
            icon={<ThunderboltOutlined />}
            onClick={() => setGenerateModalOpen(true)}
            disabled={disabled}
          >
            Generate Vouchers
          </Button>
          <Button
            icon={<DownloadOutlined />}
            onClick={handleExport}
            disabled={disabled || vouchers.length === 0}
          >
            Export
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
        title="Create Voucher Code"
        open={createModalOpen}
        onOk={handleCreate}
        onCancel={() => {
          setCreateModalOpen(false)
          createForm.resetFields()
        }}
        okText="Create"
      >
        <Form form={createForm} layout="vertical">
          <Form.Item
            name="code"
            label="Code"
            rules={[{ required: true, message: 'Please enter code' }]}
          >
            <Input placeholder="Enter voucher code" />
          </Form.Item>
        </Form>
      </Modal>

      {/* Generate Modal */}
      <Modal
        title="Generate Voucher Codes"
        open={generateModalOpen}
        onOk={handleGenerate}
        onCancel={() => {
          setGenerateModalOpen(false)
          generateForm.resetFields()
        }}
        okText="Generate"
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
            label="Amount"
            rules={[{ required: true, message: 'Please enter amount' }]}
          >
            <InputNumber min={1} style={{ width: '100%' }} placeholder="Number of codes to generate" />
          </Form.Item>

          <Form.Item
            name="length"
            label="Length"
            rules={[{ required: true, message: 'Please enter length' }]}
          >
            <InputNumber min={1} style={{ width: '100%' }} placeholder="Code length" />
          </Form.Item>

          <Form.Item
            name="format"
            label="Format"
            rules={[{ required: true }]}
          >
            <Select>
              <Select.Option value="alphanumeric">Alphanumeric</Select.Option>
              <Select.Option value="alphabetic">Alphabetic</Select.Option>
              <Select.Option value="numeric">Numeric</Select.Option>
            </Select>
          </Form.Item>

          <Form.Item name="prefix" label="Prefix">
            <Input placeholder="Optional prefix" />
          </Form.Item>

          <Form.Item name="suffix" label="Suffix">
            <Input placeholder="Optional suffix" />
          </Form.Item>

          <Form.Item name="hyphensOn" label="Hyphens On">
            <InputNumber min={0} style={{ width: '100%' }} placeholder="Insert hyphens every N characters" />
          </Form.Item>
        </Form>
      </Modal>
    </div>
  )
}
