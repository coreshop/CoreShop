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
    </div>
  )
}

export default CoreShopWidget