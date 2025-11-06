/**
 * CoreShop PimcoreBundle Studio Plugin
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
import { Input, Button, Space, Spin } from 'antd'
import { DeleteOutlined, FileImageOutlined, LoadingOutlined } from '@ant-design/icons'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'
import { loadElementDetails } from '@coreshop/resource/src/entities/api/helperApi'

interface AssetSelectProps {
  value?: number | null
  onChange?: (value: number | null) => void
  placeholder?: string
  accept?: string | string[]  // Asset types to accept, e.g., 'asset', 'asset:image', 'asset:document'
}

export const AssetSelect: React.FC<AssetSelectProps> = ({
  value,
  onChange,
  placeholder = 'Drop asset here or enter asset ID',
  accept = 'asset'
}) => {
  const [displayValue, setDisplayValue] = React.useState<string>('')
  const [loading, setLoading] = React.useState<boolean>(false)

  // Load asset path when value changes
  React.useEffect(() => {
    if (!value) {
      setDisplayValue('')
      return
    }

    setLoading(true)
    loadElementDetails([String(value)], 'asset')
      .then(details => {
        const detail = details[String(value)]
        if (detail && detail.fullPath) {
          setDisplayValue(detail.fullPath)
        } else {
          setDisplayValue(`Asset #${value}`)
        }
      })
      .catch(err => {
        console.error('Failed to load asset details:', err)
        setDisplayValue(`Asset #${value}`)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [value])

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newValue = e.target.value
    setDisplayValue(newValue)

    if (newValue === '') {
      onChange?.(null)
    } else {
      const numValue = parseInt(newValue, 10)
      if (!isNaN(numValue)) {
        onChange?.(numValue)
      }
    }
  }

  const handleClear = () => {
    setDisplayValue('')
    onChange?.(null)
  }

  const handleDrop = (info: any) => {
    const droppedId = info?.data?.id
    if (typeof droppedId === 'number') {
      onChange?.(droppedId)
    }
  }

  return (
    <DroppableEntity
      accept={accept}
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={handleDrop}
    >
      <Space.Compact style={{ width: '100%' }}>
        <Input
          value={displayValue}
          onChange={handleInputChange}
          placeholder={placeholder}
          prefix={loading ? <Spin indicator={<LoadingOutlined spin />} size="small" /> : <FileImageOutlined />}
          readOnly={loading}
        />
        {value && !loading && (
          <Button
            icon={<DeleteOutlined />}
            onClick={handleClear}
            danger
          />
        )}
      </Space.Compact>
    </DroppableEntity>
  )
}
