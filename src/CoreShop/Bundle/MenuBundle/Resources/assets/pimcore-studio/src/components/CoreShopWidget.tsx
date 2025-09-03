/**
 * CoreShop Widget Component
 * 
 * Simple React component for CoreShop menu widgets in Pimcore Studio UI
 */

import * as React from 'react'
import { CoreShopMenuItem } from '../types'

interface CoreShopWidgetProps {
  item: CoreShopMenuItem
}

export const CoreShopWidget: React.FC<CoreShopWidgetProps> = ({ item }) => {
  return (
    <div style={{ padding: '20px' }}>
      <h2>{item.label}</h2>
      <p>CoreShop {item.label} functionality will be implemented here.</p>
      {item.path && <p><strong>Path:</strong> {item.path}</p>}
      {item.permission && <p><strong>Permission:</strong> {item.permission}</p>}

      <div style={{ marginTop: '20px', padding: '15px', backgroundColor: '#f5f5f5', borderRadius: '4px' }}>
        <h3>Next Steps:</h3>
        <ul>
          <li>Implement specific functionality for {item.label}</li>
          <li>Add proper routing and navigation</li>
          <li>Connect to CoreShop backend APIs</li>
          <li>Add proper styling and UI components</li>
        </ul>
      </div>
    </div>
  )
}

export default CoreShopWidget