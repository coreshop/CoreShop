/**
 * CoreShop Widget Component
 *
 * Simple React component for CoreShop menu widgets in Pimcore Studio UI
 */

import * as React from 'react'
import { CoreShopMenuItem } from '../types'
import { useTranslation } from 'react-i18next'

interface CoreShopWidgetProps {
  item: CoreShopMenuItem
}

export const CoreShopWidget: React.FC<CoreShopWidgetProps> = ({ item }) => {
  const { t } = useTranslation()

  return (
    <div style={{ padding: '20px' }}>
      <h2>{t(item.label, { defaultValue: item.label })}</h2>
    </div>
  )
}

export default CoreShopWidget