/**
 * CoreShop IndexBundle Field Edit Modal
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
import { Modal, Form, Input, Select, Button, Tabs } from 'antd'
import { createStyles } from 'antd-style'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { IndexColumn, IndexConfig } from '../api'
import { InterpreterSchemaProvider } from '../InterpreterSchemaContext'
import { mergeFormDraft } from '../mergeFormDraft'

const { TabPane } = Tabs

interface FieldEditModalProps {
  open: boolean
  field: IndexColumn | null
  config?: IndexConfig
  onClose: () => void
  onSave: (field: IndexColumn) => void
}

export const FieldEditModal: React.FC<FieldEditModalProps> = ({
  open,
  field,
  config,
  onClose,
  onSave
}) => {
  const { styles } = useModalStyles()
  const [form] = Form.useForm()
  const [selectedGetter, setSelectedGetter] = useState<string | undefined>()
  const [selectedInterpreter, setSelectedInterpreter] = useState<string | undefined>()
  const [getterConfig, setGetterConfig] = useState<Record<string, any>>({})
  const [interpreterConfig, setInterpreterConfig] = useState<Record<string, any>>({})
  const filterSelectOption = (input: string, option?: { label?: React.ReactNode; value?: string | number }) => {
    const term = input.toLowerCase()
    const label = (option?.label ?? '').toString().toLowerCase()
    const value = (option?.value ?? '').toString().toLowerCase()

    return label.includes(term) || value.includes(term)
  }

  useEffect(() => {
    if (field && open) {
      form.setFieldsValue({
        objectKey: field.objectKey || '',
        name: field.name || '',
        getter: field.getter || undefined,
        interpreter: field.interpreter || undefined,
        columnType: field.columnType || 'INTEGER'
      })
      setSelectedGetter(field.getter)
      setSelectedInterpreter(field.interpreter)
      setGetterConfig(field.getterConfig || {})
      setInterpreterConfig(field.interpreterConfig || {})
    }
  }, [field, open, form])

  const handleGetterChange = (value: string | undefined) => {
    setSelectedGetter(value)
    setGetterConfig({}) // Reset config
  }

  const handleInterpreterChange = (value: string | undefined) => {
    setSelectedInterpreter(value)
    setInterpreterConfig({}) // Reset config
  }

  const handleGetterConfigChange = (newConfig: Record<string, any>) => {
    setGetterConfig((prev) => mergeFormDraft(prev, newConfig))
  }

  const handleInterpreterConfigChange = (newConfig: Record<string, any>) => {
    setInterpreterConfig((prev) => mergeFormDraft(prev, newConfig))
  }

  const interpreterSchemaByType = React.useMemo(() => {
    const map: Record<string, string> = {}
    for (const i of config?.interpreters ?? []) {
      if (i.blockPrefix) {
        map[i.type] = i.blockPrefix
      }
    }
    return map
  }, [config?.interpreters])

  const handleApply = () => {
    form.validateFields().then(values => {
      if (field) {
        onSave({
          ...field,
          objectKey: values.objectKey,
          name: values.name,
          getter: values.getter || null,
          getterConfig: Object.keys(getterConfig).length > 0 ? getterConfig : undefined,
          interpreter: values.interpreter || null,
          interpreterConfig: Object.keys(interpreterConfig).length > 0 ? interpreterConfig : undefined,
          columnType: values.columnType
        })
      }
      onClose()
    })
  }

  if (!field) return null

  const title = `Field (${field.name || field.objectKey || 'Unknown'})`

  // Get getter and interpreter options from config
  const getterOptions = config?.getters?.map(g => ({
    label: g.name,
    value: g.type
  })) || []

  const interpreterOptions = config?.interpreters?.map(i => ({
    label: i.name,
    value: i.type
  })) || []

  // Available column types
  const typeOptions = [
    { label: 'Integer', value: 'INTEGER' },
    { label: 'String', value: 'STRING' },
    { label: 'Text', value: 'TEXT' },
    { label: 'Double', value: 'DOUBLE' },
    { label: 'Boolean', value: 'BOOLEAN' },
    { label: 'Date', value: 'DATE' },
    { label: 'Datetime', value: 'DATETIME' }
  ]

  // Resolve getter block prefix from config
  const getterBlockPrefix = selectedGetter ? config?.getters?.find(g => g.type === selectedGetter)?.blockPrefix : undefined

  const interpreterBlockPrefix = selectedInterpreter
    ? config?.interpreters?.find(i => i.type === selectedInterpreter)?.blockPrefix
    : undefined

  return (
    <Modal
      open={open}
      onCancel={onClose}
      title={title}
      width={800}
      footer={[
        <Button
          key="apply"
          type="primary"
          onClick={handleApply}
          icon={<span>✓</span>}
        >
          Apply
        </Button>,
      ]}
      className={styles.modal}
      destroyOnClose
    >
      <div className={styles.content}>
        <Tabs defaultActiveKey="settings" className={styles.tabs}>
          <TabPane tab="Settings" key="settings">
            <Form
              form={form}
              layout="horizontal"
              labelCol={{ span: 6 }}
              wrapperCol={{ span: 18 }}
              className={styles.form}
            >
              <Form.Item
                label="Key"
                name="objectKey"
                rules={[{ required: true, message: 'Please enter a key' }]}
              >
                <Input placeholder="Enter field key" readOnly disabled />
              </Form.Item>

              <Form.Item
                label="Name"
                name="name"
                rules={[{ required: true, message: 'Please enter a name' }]}
              >
                <Input placeholder="Enter field name" />
              </Form.Item>

              <Form.Item
                label="Type"
                name="columnType"
                rules={[{ required: true, message: 'Please select a type' }]}
              >
                <Select
                  placeholder="Select column type"
                  options={typeOptions}
                  showSearch
                  optionFilterProp="label"
                  filterOption={filterSelectOption}
                />
              </Form.Item>

              {/* Getter Section */}
              <div className={styles.section}>
                <div className={styles.sectionTitle}>Getter Configuration</div>
                <Form.Item
                  label="Getter Class"
                  name="getter"
                >
                  <Select
                    placeholder="Select getter class"
                    allowClear
                    options={getterOptions}
                    showSearch
                    optionFilterProp="label"
                    filterOption={filterSelectOption}
                    onChange={handleGetterChange}
                  />
                </Form.Item>

                {/* Getter Configuration Fields - SchemaForm driven */}
                {selectedGetter && getterBlockPrefix && (
                  <div className={styles.configFields}>
                    <SchemaForm
                      blockPrefix={getterBlockPrefix}
                      embedded
                      data={getterConfig}
                      onChange={handleGetterConfigChange}
                    />
                  </div>
                )}
              </div>

              {/* Interpreter Section */}
              <div className={styles.section}>
                <div className={styles.sectionTitle}>Interpreter Configuration</div>
                <Form.Item
                  label="Interpreter"
                  name="interpreter"
                >
                  <Select
                    placeholder="Select interpreter"
                    allowClear
                    options={interpreterOptions}
                    showSearch
                    optionFilterProp="label"
                    filterOption={filterSelectOption}
                    onChange={handleInterpreterChange}
                  />
                </Form.Item>

                {/* Interpreter Configuration Fields - SchemaForm driven */}
                {selectedInterpreter && interpreterBlockPrefix && (
                  <div className={styles.configFields}>
                    <InterpreterSchemaProvider value={interpreterSchemaByType}>
                      <SchemaForm
                        blockPrefix={interpreterBlockPrefix}
                        embedded
                        data={interpreterConfig}
                        onChange={handleInterpreterConfigChange}
                      />
                    </InterpreterSchemaProvider>
                  </div>
                )}
              </div>
            </Form>
          </TabPane>
        </Tabs>
      </div>
    </Modal>
  )
}

const useModalStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal-content {
      display: flex;
      flex-direction: column;
      max-height: calc(100vh - 120px);
    }

    .ant-modal-header {
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }

    .ant-modal-body {
      flex: 1;
      min-height: 0;
      overflow: auto;
      padding: 0;
      background: ${token.colorBgElevated};
    }

    .ant-modal-footer {
      padding: 12px 16px;
      margin-top: 0;
      background: ${token.colorBgContainer};
      border-top: 1px solid ${token.colorBorderSecondary};

      .ant-btn-primary {
        background: ${token.colorSuccess};
        border-color: ${token.colorSuccess};

        &:hover {
          background: ${token.colorSuccessHover};
          border-color: ${token.colorSuccessHover};
        }
      }
    }

    .ant-modal-close {
      color: ${token.colorTextSecondary};

      &:hover {
        color: ${token.colorText};
        background: ${token.colorBgTextHover};
      }
    }
  `,
  content: css`
    display: flex;
    flex-direction: column;
    min-height: 0;
  `,
  tabs: css`
    .ant-tabs-nav {
      margin: 0;
      padding: 0 16px;
      background: ${token.colorBgContainer};
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }

    .ant-tabs-content {
      padding: 24px;
      background: ${token.colorBgElevated};
    }
  `,
  form: css`
    .ant-form-item {
      margin-bottom: 16px;
    }

    .ant-form-item-label {
      > label {
        font-weight: 500;
        color: ${token.colorText};
      }
    }

    .ant-select,
    .ant-input {
      width: 100%;
    }
  `,
  section: css`
    margin-top: 24px;
    padding: 16px;
    background: ${token.colorFillQuaternary};
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: 6px;

    &:first-of-type {
      margin-top: 16px;
    }
  `,
  sectionTitle: css`
    font-size: 13px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
  `,
  configFields: css`
    margin-top: 8px;
    padding: 12px;
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: 8px;
    background: ${token.colorBgContainer};

    .coreshop-schema-form-embedded .ant-form-item {
      margin-bottom: 12px;

      &:last-child {
        margin-bottom: 0;
      }
    }

    .coreshop-schema-form-embedded .ant-form-item-label {
      padding-bottom: 6px;
    }

    .coreshop-schema-form-embedded .ant-form-item-control {
      min-width: 0;
      width: 100%;
    }

    .coreshop-interpreter-collection__add {
      position: static !important;
      transform: none !important;
    }

    .coreshop-interpreter-collection .ant-card {
      border-color: ${token.colorBorderSecondary};
      background: ${token.colorBgContainer};
    }

    .coreshop-interpreter-collection .ant-card-head {
      min-height: 40px;
      background: ${token.colorFillTertiary};
      border-bottom-color: ${token.colorBorderSecondary};
    }

    .coreshop-interpreter-collection .ant-card-head-title {
      padding: 8px 0;
    }
  `
}))
