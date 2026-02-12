/**
 * CoreShop IndexBundle Column Configuration Dialog
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
import { Modal, Form, Input, Select, Tabs, Collapse } from 'antd'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import type { IndexColumn, IndexConfig } from '../api'
import type { GetterConfiguratorRegistry, InterpreterConfiguratorRegistry } from '../registry'
import { serviceIds } from '../service-ids'
import { DefaultGetterConfigurator, DefaultInterpreterConfigurator } from '../configurators'
import { getErrorMessage } from '@coreshop/resource/src/entities'

interface ColumnConfigDialogProps {
  column: IndexColumn | null
  config: IndexConfig
  workerType?: string
  visible: boolean
  onOk: (column: IndexColumn) => void
  onCancel: () => void
}

export const ColumnConfigDialog: React.FC<ColumnConfigDialogProps> = ({
  column,
  config,
  workerType,
  visible,
  onOk,
  onCancel
}) => {
  const [form] = Form.useForm()
  const messageApi = useMessage()
  const [selectedGetter, setSelectedGetter] = React.useState<string | undefined>(undefined)
  const [selectedInterpreter, setSelectedInterpreter] = React.useState<string | undefined>(undefined)
  const [getterConfig, setGetterConfig] = React.useState<Record<string, any>>({})
  const [interpreterConfig, setInterpreterConfig] = React.useState<Record<string, any>>({})

  // Load form data when dialog opens
  React.useEffect(() => {
    if (visible && column) {
      form.setFieldsValue({
        objectKey: column.objectKey,
        name: column.name,
        columnType: column.columnType,
        getter: column.getter,
        interpreter: column.interpreter
      })
      setSelectedGetter(column.getter)
      setSelectedInterpreter(column.interpreter)
      setGetterConfig(column.getterConfig || {})
      setInterpreterConfig(column.interpreterConfig || {})
    } else if (visible) {
      form.resetFields()
      setSelectedGetter(undefined)
      setSelectedInterpreter(undefined)
      setGetterConfig({})
      setInterpreterConfig({})
    }
  }, [visible, column, form])

  const handleOk = async () => {
    try {
      const values = await form.validateFields()
      onOk({
        ...column,
        objectKey: values.objectKey,
        name: values.name,
        columnType: values.columnType,
        getter: values.getter,
        getterConfig: selectedGetter ? getterConfig : undefined,
        interpreter: values.interpreter,
        interpreterConfig: selectedInterpreter ? interpreterConfig : undefined
      } as IndexColumn)
    } catch (error) {
      void messageApi.error(getErrorMessage(error, 'Validation failed'))
    }
  }

  const handleGetterChange = (value: string) => {
    setSelectedGetter(value)
    // Reset getter config when changing getter type
    if (!column?.getter || column.getter !== value) {
      setGetterConfig({})
    }
  }

  const handleInterpreterChange = (value: string) => {
    setSelectedInterpreter(value)
    // Reset interpreter config when changing interpreter type
    if (!column?.interpreter || column.interpreter !== value) {
      setInterpreterConfig({})
    }
  }

  // Get configurator registries
  const getterConfiguratorRegistry = React.useMemo(
    () => container.get<GetterConfiguratorRegistry>(serviceIds.getterConfiguratorRegistry),
    []
  )

  const interpreterConfiguratorRegistry = React.useMemo(
    () => container.get<InterpreterConfiguratorRegistry>(serviceIds.interpreterConfiguratorRegistry),
    []
  )

  // Get configurator components
  const GetterConfiguratorComponent = selectedGetter
    ? (getterConfiguratorRegistry.get(selectedGetter)?.component ?? DefaultGetterConfigurator)
    : null

  const InterpreterConfiguratorComponent = selectedInterpreter
    ? (interpreterConfiguratorRegistry.get(selectedInterpreter)?.component ?? DefaultInterpreterConfigurator)
    : null

  // Get field types for the selected worker
  const fieldTypeOptions = workerType && config.fieldTypes[workerType]
    ? config.fieldTypes[workerType].map(ft => ({
        label: ft.name,
        value: ft.type
      }))
    : []

  // Getter options
  const getterOptions = config.getters.map(g => ({
    label: g.name,
    value: g.type
  }))

  // Interpreter options
  const interpreterOptions = config.interpreters.map(i => ({
    label: i.name,
    value: i.type
  }))

  return (
    <Modal
      title={column?.id ? 'Edit Index Field' : 'Configure Index Field'}
      open={visible}
      onOk={handleOk}
      onCancel={onCancel}
      width={800}
      destroyOnClose
    >
      <Form
        form={form}
        layout="vertical"
        preserve={false}
      >
        <Tabs
          items={[
            {
              key: 'basic',
              label: 'Basic',
              children: (
                <>
                  <Form.Item
                    label="Object Key"
                    name="objectKey"
                    help="Original field name from Pimcore class"
                  >
                    <Input disabled />
                  </Form.Item>

                  <Form.Item
                    label="Name"
                    name="name"
                    rules={[{ required: true, message: 'Name is required' }]}
                    help="Display name in index"
                  >
                    <Input placeholder="Field name" />
                  </Form.Item>

                  {fieldTypeOptions.length > 0 && (
                    <Form.Item
                      label="Column Type"
                      name="columnType"
                      help="Database column type for indexing"
                      rules={[{ required: true, message: 'Column type is required' }]}
                    >
                      <Select
                        options={fieldTypeOptions}
                        placeholder="Select column type"
                        showSearch
                      />
                    </Form.Item>
                  )}
                </>
              )
            },
            {
              key: 'advanced',
              label: 'Advanced',
              children: (
                <Collapse
                  items={[
                    {
                      key: 'getter',
                      label: 'Getter Configuration',
                      children: (
                        <>
                          <Form.Item
                            label="Getter Type"
                            name="getter"
                            help="How to retrieve the value from Pimcore object"
                          >
                            <Select
                              options={getterOptions}
                              placeholder="Select getter"
                              allowClear
                              showSearch
                              onChange={handleGetterChange}
                            />
                          </Form.Item>

                          {GetterConfiguratorComponent && (
                            <div style={{ marginTop: 16, padding: 16, background: '#fafafa', borderRadius: 4 }}>
                              <h4 style={{ marginTop: 0, marginBottom: 16 }}>Getter Parameters</h4>
                              <GetterConfiguratorComponent
                                config={getterConfig}
                                onChange={setGetterConfig}
                              />
                            </div>
                          )}
                        </>
                      )
                    },
                    {
                      key: 'interpreter',
                      label: 'Interpreter Configuration',
                      children: (
                        <>
                          <Form.Item
                            label="Interpreter Type"
                            name="interpreter"
                            help="How to transform/interpret the value"
                          >
                            <Select
                              options={interpreterOptions}
                              placeholder="Select interpreter"
                              allowClear
                              showSearch
                              onChange={handleInterpreterChange}
                            />
                          </Form.Item>

                          {InterpreterConfiguratorComponent && (
                            <div style={{ marginTop: 16, padding: 16, background: '#fafafa', borderRadius: 4 }}>
                              <h4 style={{ marginTop: 0, marginBottom: 16 }}>Interpreter Parameters</h4>
                              <InterpreterConfiguratorComponent
                                config={interpreterConfig}
                                onChange={setInterpreterConfig}
                              />
                            </div>
                          )}
                        </>
                      )
                    }
                  ]}
                />
              )
            }
          ]}
        />
      </Form>
    </Modal>
  )
}
