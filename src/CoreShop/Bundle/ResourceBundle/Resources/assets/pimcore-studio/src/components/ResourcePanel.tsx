/**
 * CoreShop Resource Panel Component
 * 
 * React component that replaces the ExtJS resource panel functionality
 * Provides CRUD operations for CoreShop resources
 */

import React, { useState, useEffect, useCallback } from 'react'
import { 
  Table, 
  Button, 
  Modal, 
  Input, 
  message, 
  Space, 
  Popconfirm,
  Layout,
  Typography,
  Card
} from 'antd'
import { PlusOutlined, DeleteOutlined, ReloadOutlined } from '@ant-design/icons'
import { ResourceService } from '@/services/ResourceService'
import { CoreShopResource, ResourcePanelConfig, GridColumn } from '@/types'

const { Title } = Typography
const { Content } = Layout

interface ResourcePanelProps {
  config: ResourcePanelConfig
  columns?: GridColumn[]
  onItemSelect?: (item: CoreShopResource) => void
}

export const ResourcePanel: React.FC<ResourcePanelProps> = ({ 
  config, 
  columns = [],
  onItemSelect 
}) => {
  const [data, setData] = useState<CoreShopResource[]>([])
  const [loading, setLoading] = useState(false)
  const [isAddModalVisible, setIsAddModalVisible] = useState(false)
  const [newItemName, setNewItemName] = useState('')
  
  const resourceService = new ResourceService()

  // Load data
  const loadData = useCallback(async () => {
    setLoading(true)
    try {
      const response = await resourceService.getList(config.routing.list)
      if (response.success) {
        setData(response.data)
      } else {
        message.error('Failed to load data')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      message.error('Failed to load data')
    } finally {
      setLoading(false)
    }
  }, [config.routing.list])

  useEffect(() => {
    loadData()
  }, [loadData])

  // Add new item
  const handleAdd = async () => {
    if (!newItemName.trim()) {
      message.warning('Please enter a name')
      return
    }

    try {
      const response = await resourceService.create(config.routing.add, {
        name: newItemName.trim()
      })
      
      if (response.success) {
        message.success('Item added successfully')
        setIsAddModalVisible(false)
        setNewItemName('')
        loadData()
        
        // Open the new item if callback provided
        if (onItemSelect && response.data) {
          onItemSelect(response.data)
        }
      } else {
        message.error('Failed to add item')
      }
    } catch (error) {
      console.error('Error adding item:', error)
      message.error('Failed to add item')
    }
  }

  // Delete item
  const handleDelete = async (record: CoreShopResource) => {
    try {
      await resourceService.delete(config.routing.delete, record.id)
      message.success('Item deleted successfully')
      loadData()
    } catch (error) {
      console.error('Error deleting item:', error)
      message.error('Failed to delete item')
    }
  }

  // Default columns if none provided
  const defaultColumns: GridColumn[] = [
    {
      title: 'Name',
      dataIndex: 'name',
      render: (value: string, record: CoreShopResource) => (
        <Button 
          type="link" 
          onClick={() => onItemSelect?.(record)}
          title={`ID: ${record.id}`}
        >
          {value}
        </Button>
      )
    }
  ]

  // Convert to Ant Design table columns
  const tableColumns = (columns.length > 0 ? columns : defaultColumns).concat([
    {
      title: 'Actions',
      dataIndex: 'actions',
      width: 100,
      render: (_: any, record: CoreShopResource) => (
        <Space>
          <Popconfirm
            title="Are you sure you want to delete this item?"
            onConfirm={() => handleDelete(record)}
            okText="Yes"
            cancelText="No"
          >
            <Button
              type="text"
              icon={<DeleteOutlined />}
              danger
              size="small"
            />
          </Popconfirm>
        </Space>
      )
    }
  ]).map(col => ({
    ...col,
    key: col.dataIndex
  }))

  return (
    <Layout style={{ height: '100%' }}>
      <Content style={{ padding: '16px' }}>
        <Card>
          <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <Title level={4} style={{ margin: 0 }}>
              {config.title}
            </Title>
            <Space>
              <Button
                type="primary"
                icon={<PlusOutlined />}
                onClick={() => setIsAddModalVisible(true)}
              >
                Add
              </Button>
              <Button
                icon={<ReloadOutlined />}
                onClick={loadData}
                loading={loading}
              >
                Refresh
              </Button>
            </Space>
          </div>

          <Table
            dataSource={data}
            columns={tableColumns}
            loading={loading}
            rowKey="id"
            pagination={{
              showSizeChanger: true,
              showQuickJumper: true,
              showTotal: (total) => `Total ${total} items`
            }}
            size="middle"
          />
        </Card>

        <Modal
          title="Add New Item"
          open={isAddModalVisible}
          onOk={handleAdd}
          onCancel={() => {
            setIsAddModalVisible(false)
            setNewItemName('')
          }}
          okText="Add"
          cancelText="Cancel"
        >
          <Input
            placeholder="Enter name"
            value={newItemName}
            onChange={(e) => setNewItemName(e.target.value)}
            onPressEnter={handleAdd}
            autoFocus
          />
        </Modal>
      </Content>
    </Layout>
  )
}

export default ResourcePanel