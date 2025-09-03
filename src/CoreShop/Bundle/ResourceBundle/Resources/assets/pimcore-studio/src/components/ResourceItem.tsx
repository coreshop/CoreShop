/**
 * CoreShop Resource Item Component
 * 
 * React component for editing individual resource items
 * Replaces the ExtJS-based item forms
 */

import React, { useState, useEffect } from 'react'
import { 
  Form, 
  Input, 
  Button, 
  message, 
  Card, 
  Space,
  Typography,
  Spin
} from 'antd'
import { SaveOutlined, UndoOutlined } from '@ant-design/icons'
import { ResourceService } from '@/services/ResourceService'
import { CoreShopResource, ResourceItemConfig } from '@/types'

const { Title } = Typography

interface ResourceItemProps {
  config: ResourceItemConfig
  onSave?: (item: CoreShopResource) => void
  onCancel?: () => void
  children?: React.ReactNode
}

export const ResourceItem: React.FC<ResourceItemProps> = ({ 
  config, 
  onSave, 
  onCancel,
  children 
}) => {
  const [form] = Form.useForm()
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [data, setData] = useState<CoreShopResource>(config.data)
  
  const resourceService = new ResourceService()

  // Load fresh data if ID is provided
  useEffect(() => {
    if (config.data.id) {
      loadData()
    }
  }, [config.data.id])

  const loadData = async () => {
    if (!config.data.id) return
    
    setLoading(true)
    try {
      const response = await resourceService.getItem(config.routing.get, config.data.id)
      if (response.success) {
        setData(response.data)
        form.setFieldsValue(response.data)
      } else {
        message.error('Failed to load item data')
      }
    } catch (error) {
      console.error('Error loading item data:', error)
      message.error('Failed to load item data')
    } finally {
      setLoading(false)
    }
  }

  const handleSave = async (values: any) => {
    setSaving(true)
    try {
      const response = await resourceService.update(
        config.routing.get.replace('/get', ''), 
        data.id, 
        values
      )
      
      if (response.success) {
        message.success('Item saved successfully')
        setData(response.data)
        onSave?.(response.data)
      } else {
        message.error('Failed to save item')
      }
    } catch (error) {
      console.error('Error saving item:', error)
      message.error('Failed to save item')
    } finally {
      setSaving(false)
    }
  }

  const handleReset = () => {
    form.setFieldsValue(data)
    message.info('Form reset to saved values')
  }

  return (
    <Card style={{ height: '100%' }}>
      <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Title level={4} style={{ margin: 0 }}>
          {data.name ? `Edit: ${data.name}` : 'New Item'}
        </Title>
        <Space>
          <Button
            type="default"
            icon={<UndoOutlined />}
            onClick={handleReset}
          >
            Reset
          </Button>
          <Button
            type="primary"
            icon={<SaveOutlined />}
            onClick={() => form.submit()}
            loading={saving}
          >
            Save
          </Button>
          {onCancel && (
            <Button onClick={onCancel}>
              Cancel
            </Button>
          )}
        </Space>
      </div>

      <Spin spinning={loading}>
        <Form
          form={form}
          layout="vertical"
          initialValues={data}
          onFinish={handleSave}
        >
          <Form.Item
            name="name"
            label="Name"
            rules={[
              { required: true, message: 'Please enter a name' },
              { min: 2, message: 'Name must be at least 2 characters' }
            ]}
          >
            <Input placeholder="Enter name" />
          </Form.Item>

          {/* Custom fields can be passed as children */}
          {children}

          {/* Hidden ID field for updates */}
          <Form.Item name="id" hidden>
            <Input />
          </Form.Item>
        </Form>
      </Spin>
    </Card>
  )
}

export default ResourceItem