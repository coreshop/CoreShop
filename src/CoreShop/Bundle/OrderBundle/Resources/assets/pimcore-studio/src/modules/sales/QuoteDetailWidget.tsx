/**
 * CoreShop OrderBundle - Quote Detail Widget
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
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { SaleDetail } from './SaleDetail'
import { SaleDetailSkeleton } from './SaleDetailSkeleton'
import type { Sale } from './types'

interface QuoteDetailWidgetProps {
  orderId: number
}

const loadQuoteFromBackend = async (id: number): Promise<Sale | null> => {
  try {
    const response = await fetch(`/pimcore-studio/api/coreshop/order/detail?id=${id}&saleType=quote`, {
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
    console.error('Error loading quote:', error)
    throw error
  }
}

export const QuoteDetailWidget: React.FC<QuoteDetailWidgetProps> = (props) => {
  const [quote, setQuote] = React.useState<Sale | undefined>()
  const [loading, setLoading] = React.useState(true)
  const messageApi = useMessage()

  const quoteId = props?.orderId

  // Note: messageApi is intentionally excluded from deps to prevent infinite loops
  const loadQuote = React.useCallback(async () => {
    if (!quoteId) {
      setLoading(false)
      return
    }

    try {
      setLoading(true)
      const data = await loadQuoteFromBackend(quoteId)

      if (data) {
        setQuote(data)
      } else {
        void messageApi.error(renderApiError('Failed to load quote data'))
      }
    } catch (error) {
      void messageApi.error(renderApiError('Error loading quote'))
    } finally {
      setLoading(false)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [quoteId])

  React.useEffect(() => {
    void loadQuote()
  }, [loadQuote])

  const handleChange = React.useCallback(async (updates: Partial<Sale>) => {
    setQuote(prev => prev ? { ...prev, ...updates } : prev)

    try {
      const response = await fetch(`/pimcore-studio/api/coreshop/order/update/${quoteId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      })

      if (!response.ok) {
        throw new Error('Failed to save')
      }

      void messageApi.success('Quote updated successfully')
      await loadQuote()
    } catch (error) {
      void messageApi.error(renderApiError('Failed to save changes'))
      await loadQuote()
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [quoteId, loadQuote])

  if (loading) {
    return <SaleDetailSkeleton />
  }

  if (!quote) {
    return (
      <div style={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        height: '100%',
        width: '100%',
        minHeight: 400,
        flexDirection: 'column',
        gap: 16
      }}>
        <h2>Quote not found</h2>
        <p style={{ color: '#999' }}>Quote #{quoteId} could not be loaded</p>
      </div>
    )
  }

  return (
    <SaleDetail
      sale={quote}
      type="quote"
      onChange={handleChange}
      onReload={loadQuote}
    />
  )
}
