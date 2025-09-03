/**
 * MessengerChart Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import * as React from 'react'
import { Card, Spin, Alert, Typography } from 'antd'
import { useMessengerChart } from '../hooks/useMessenger'

const { Text } = Typography

export const MessengerChart: React.FC = () => {
  const { data, loading, error } = useMessengerChart()

  if (error) {
    return (
      <Alert
        message="Error loading chart data"
        description={error}
        type="error"
        style={{ margin: '16px 0' }}
      />
    )
  }

  if (loading) {
    return (
      <Card style={{ height: 200, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <Spin size="large" />
      </Card>
    )
  }

  // Simple fallback chart implementation without external dependencies
  const maxCount = Math.max(...data.map(item => item.count), 1)

  return (
    <Card style={{ height: 200 }}>
      <div style={{ display: 'flex', alignItems: 'end', height: '120px', gap: '8px', padding: '16px 0' }}>
        {data.length === 0 ? (
          <div style={{ width: '100%', textAlign: 'center', paddingTop: '40px' }}>
            <Text type="secondary">No data available</Text>
          </div>
        ) : (
          data.map((item, index) => {
            const height = (item.count / maxCount) * 100
            return (
              <div 
                key={index}
                style={{ 
                  display: 'flex', 
                  flexDirection: 'column', 
                  alignItems: 'center',
                  flex: 1,
                  minWidth: '60px'
                }}
              >
                <div 
                  style={{ 
                    width: '100%',
                    height: `${height}px`,
                    backgroundColor: '#1890ff',
                    marginBottom: '4px',
                    borderRadius: '2px 2px 0 0',
                    display: 'flex',
                    alignItems: 'flex-start',
                    justifyContent: 'center',
                    paddingTop: '2px'
                  }}
                >
                  <Text style={{ fontSize: '11px', color: 'white', fontWeight: 'bold' }}>
                    {item.count}
                  </Text>
                </div>
                <Text 
                  style={{ 
                    fontSize: '10px', 
                    textAlign: 'center',
                    wordBreak: 'break-all',
                    lineHeight: '12px',
                    maxWidth: '100%'
                  }}
                  ellipsis={{ tooltip: item.receiver }}
                >
                  {item.receiver}
                </Text>
              </div>
            )
          })
        )}
      </div>
    </Card>
  )
}

export default MessengerChart