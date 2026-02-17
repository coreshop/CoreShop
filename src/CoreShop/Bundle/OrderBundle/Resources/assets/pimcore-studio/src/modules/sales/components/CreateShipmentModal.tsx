/**
 * CoreShop OrderBundle Create Shipment Modal
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useState, useEffect } from 'react'
import { Modal } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { SchemaForm } from '@coreshop/studio-form'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { formatCurrency } from '@coreshop/pimcore/src/utils'
import React from "react"

export interface CreateShipmentModalProps {
  open: boolean
  orderId: number
  currencyCode: string
  carrierId?: number
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Create Shipment Modal
 *
 * Form fields and items grid rendered via SchemaForm.
 * Columns are defined by OrderShipmentCreationItemsType (configurable via form types).
 */
export const CreateShipmentModal: React.FC<CreateShipmentModalProps> = ({
  open,
  orderId,
  currencyCode,
  carrierId,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [formData, setFormData] = useState<Record<string, unknown>>({})
  const [loading, setLoading] = useState(false)
  const [loadingItems, setLoadingItems] = useState(false)

  // Set default carrier and load items when modal opens
  useEffect(() => {
    if (!open) return

    if (carrierId) {
      setFormData((prev) => ({ ...prev, carrier: carrierId }))
    }

    const loadItems = async () => {
      setLoadingItems(true)
      try {
        const response = await fetch(`/pimcore-studio/api/coreshop/order-shipment/get-ship-able-items?id=${orderId}`)
        const data = await response.json()

        if (data.success && data.items && Object.keys(data.items).length > 0) {
          // Map API data (keyed by orderItemId) to form field names
          const items: Record<string, any> = {}
          for (const [orderItemId, item] of Object.entries(data.items) as Array<[string, any]>) {
            items[orderItemId] = {
              orderItemId: item.orderItemId,
              name: item.name,
              price: formatCurrency(item.price, currencyCode),
              orderedQuantity: item.quantity,
              quantityShipped: item.quantityShipped,
              quantity: item.toShip,
              maxQuantity: item.maxToShip,
            }
          }
          setFormData((prev) => ({ ...prev, items }))
        } else {
          void messageApi.warning(t('coreshop_shipment_no_items', { defaultValue: 'No shippable items found' }))
          onCancel()
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_shipment_load_items_error', { defaultValue: 'Failed to load items' }))))
        onCancel()
      } finally {
        setLoadingItems(false)
      }
    }

    void loadItems()
  }, [open, orderId, carrierId, onCancel, t])

  // Handle save
  const handleSave = async () => {
    try {
      const itemsObj = (formData.items && typeof formData.items === 'object' && !Array.isArray(formData.items))
        ? formData.items as Record<string, any>
        : {}
      const itemsToShip: Record<string, any> = {}
      for (const [key, item] of Object.entries(itemsObj)) {
        if (item.quantity > 0) {
          itemsToShip[key] = {
            orderItemId: item.orderItemId,
            quantity: item.quantity,
            maxQuantity: item.maxQuantity
          }
        }
      }

      if (Object.keys(itemsToShip).length === 0) {
        void messageApi.warning(t('coreshop_shipment_select_items', { defaultValue: 'Please select items to ship' }))
        return
      }

      setLoading(true)

      const { items: _, ...otherFormData } = formData

      const payload = {
        ...otherFormData,
        id: orderId,
        items: itemsToShip
      }

      const response = await fetch('/pimcore-studio/api/coreshop/order-shipment/create-shipment', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      const data = await response.json()

      if (data.success) {
        void messageApi.success(t('coreshop_shipment_create_success', { defaultValue: 'Shipment created successfully' }))
        setFormData({})
        onSuccess()
      } else {
        void messageApi.error(renderApiError(data.message || t('coreshop_shipment_create_error', { defaultValue: 'Failed to create shipment' })))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_shipment_create_error', { defaultValue: 'Failed to create shipment' }))))
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal
      open={open}
      title={`${t('coreshop_shipment_create_new', { defaultValue: 'Create Shipment for Order' })} (${orderId})`}
      onCancel={onCancel}
      onOk={handleSave}
      okText={t('coreshop_save', { defaultValue: 'Save' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      width={900}
      confirmLoading={loading || loadingItems}
      destroyOnClose
    >
      <SchemaForm
        blockPrefix="coreshop_order_shipment_creation"
        data={formData}
        onChange={(draft) => setFormData((prev) => ({ ...prev, ...draft }))}
      />
    </Modal>
  )
}
