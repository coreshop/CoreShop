/**
 * MessengerList Component - Main component for messenger management
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
import { Tabs, Button } from 'antd'
import { ReloadOutlined } from '@ant-design/icons'
import MessengerChart from './MessengerChart'
import MessengerFailedGrid from './MessengerFailedGrid'
import MessengerPendingGrid from './MessengerPendingGrid'
import { useMessengerChart } from '../hooks/useMessenger'

const { TabPane } = Tabs

export const MessengerList: React.FC = () => {
  const { reload: reloadChart } = useMessengerChart()

  const handleGlobalReload = () => {
    reloadChart()
    // Note: Individual grid reloads are handled by their respective components
    // when receivers are selected
  }

  return (
    <div style={{ 
      height: '100%', 
      display: 'flex', 
      flexDirection: 'column',
      padding: '16px',
      overflow: 'hidden' // Prevent outer scrollbar
    }}>
      {/* Fixed height header with reload button */}
      <div style={{ 
        marginBottom: '16px',
        display: 'flex',
        justifyContent: 'flex-end',
        flexShrink: 0
      }}>
        <Button 
          type="primary" 
          icon={<ReloadOutlined />} 
          onClick={handleGlobalReload}
        >
          Reload All
        </Button>
      </div>

      {/* Fixed height chart */}
      <div style={{ marginBottom: '16px', flexShrink: 0 }}>
        <MessengerChart />
      </div>
      
      {/* Flexible tabs that take remaining space */}
      <div style={{ 
        flex: 1, 
        minHeight: 0,
        overflow: 'hidden'
      }}>
        <Tabs 
          defaultActiveKey="failed" 
          type="card"
          style={{ 
            height: '100%',
            display: 'flex',
            flexDirection: 'column'
          }}
          tabBarStyle={{ flexShrink: 0 }}
        >
          <TabPane tab="Failed Messages" key="failed" style={{ height: '100%', overflow: 'hidden' }}>
            <div style={{ 
              height: '100%',
              overflow: 'hidden'
            }}>
              <MessengerFailedGrid />
            </div>
          </TabPane>
          <TabPane tab="Pending Messages" key="pending" style={{ height: '100%', overflow: 'hidden' }}>
            <div style={{ 
              height: '100%',
              overflow: 'hidden'
            }}>
              <MessengerPendingGrid />
            </div>
          </TabPane>
        </Tabs>
      </div>
    </div>
  )
}

export default MessengerList