/**
 * CoreShop OrderBundle Sale Editor Tabs
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
import { Spin } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { SaleDetail } from './SaleDetail'
import type { SaleType, Sale } from './types'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface SaleEditorTabsProps {
  element: any // Pimcore DataObject element
  type: SaleType
}

/**
 * Loads sale details from the backend API
 */
const loadSaleFromBackend = async (id: number, type: SaleType): Promise<Sale | null> => {
  try {
    const response = await fetch(`/pimcore-studio/api/coreshop/order/detail?id=${id}&saleType=${type}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()

    if (data.success && data.sale) {
      return data.sale as Sale
    }

    return null
  } catch (error) {
    console.error('Error loading sale:', error)
    throw error
  }
}

export const SaleEditorTabs: React.FC<SaleEditorTabsProps> = ({ element, type }) => {
  const messageApi = useMessage()
  const [sale, setSale] = React.useState<Sale | undefined>()
  const [loading, setLoading] = React.useState(true)

  // Load sale data from backend
  const loadSale = React.useCallback(async () => {
    if (!element?.id) {
      setLoading(false)
      return
    }

    try {
      setLoading(true)
      const data = await loadSaleFromBackend(element.id, type)

      if (data) {
        setSale(data)
      } else {
        void messageApi.error(renderApiError('Failed to load sale data'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Error loading sale')))
    } finally {
      setLoading(false)
    }
  }, [element?.id, type])

  // Load on mount and when element/type changes
  React.useEffect(() => {
    void loadSale()
  }, [loadSale])

  const handleChange = React.useCallback(async (updates: Partial<Sale>) => {
    // Optimistic update using functional updater to avoid stale closure
    setSale(prev => prev ? { ...prev, ...updates } : prev)

    // TODO: Implement save logic through Pimcore API
    // For now just log

    // Note: If save fails, loadSale() should be called to revert
  }, [])

  if (loading) {
    return (
      <div style={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        height: '100%',
        minHeight: 400
      }}>
        <Spin size="large" tip="Loading sale details..." />
      </div>
    )
  }

  return (
    <SaleDetail
      sale={sale}
      type={type}
      onChange={handleChange}
      onReload={loadSale}
    />
  )
}
