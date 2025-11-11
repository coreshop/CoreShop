/**
 * CoreShop OrderBundle Sale Manager
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
import { EntityTabbedManager } from '@coreshop/resource'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'
import { createSaleApi } from './api'
import type { Sale, SaleType } from './types'
import { SaleDetail } from './SaleDetail'

interface SaleManagerProps {
  type: SaleType
}

export const SaleManager: React.FC<SaleManagerProps> = ({ type }) => {
  const modal = useFormModal()
  const api = React.useMemo(() => createSaleApi(type), [type])

  const titles = {
    order: 'Orders',
    cart: 'Carts',
    quote: 'Quotes'
  }

  const getTitleForSale = React.useCallback((li: any, data?: Sale) => {
    if (type === 'order') {
      return data?.saleNumber ?? li?.name ?? `Order #${data?.id ?? li?.id ?? ''}`
    }
    if (type === 'quote') {
      return data?.quoteNumber ?? li?.name ?? `Quote #${data?.id ?? li?.id ?? ''}`
    }
    return li?.name ?? `Cart #${data?.id ?? li?.id ?? ''}`
  }, [type])

  return (
    <EntityTabbedManager<Sale>
      api={api}
      dragType={`coreshop:${type}`}
      leftRootTitle={titles[type]}
      getTitle={getTitleForSale}
      buildSavePayload={(data) => {
        // Build payload for save
        const payload: any = {
          id: data.id,
          customerId: data.customerId,
          currency: data.currency,
          storeId: data.storeId,
          saleDate: data.saleDate,
          saleLanguage: data.saleLanguage,
          items: data.items?.map(item => ({
            id: item.id,
            productId: item.productId,
            productName: item.productName,
            quantity: item.quantity,
            unitPrice: item.unitPrice,
            totalPrice: item.totalPrice,
            customItemDiscount: item.customItemDiscount,
            customItemPrice: item.customItemPrice
          }))
        }

        // Add type-specific fields
        if (type === 'order') {
          if (data.orderState) payload.orderState = data.orderState
          if (data.paymentState) payload.paymentState = data.paymentState
          if (data.shipmentState) payload.shipmentState = data.shipmentState
          if (data.invoiceState) payload.invoiceState = data.invoiceState
        }

        return payload
      }}
      onAdd={async () => await new Promise<number>((resolve) => {
        modal.input({
          title: `Add ${type.charAt(0).toUpperCase() + type.slice(1)}`,
          label: 'Customer ID (optional)',
          onOk: async (value: string) => {
            const customerId = value ? parseInt(value) : undefined
            const res = await api.add({ customer: customerId })
            resolve(res.data.id!)
          }
        })
      })}
      renderDetail={(data, setData) => {
        if (!data) {
          return (
            <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
              Select a {type} to view details.
            </div>
          )
        }

        return (
          <SaleDetail
            sale={data}
            type={type}
            onChange={setData}
          />
        )
      }}
    />
  )
}
