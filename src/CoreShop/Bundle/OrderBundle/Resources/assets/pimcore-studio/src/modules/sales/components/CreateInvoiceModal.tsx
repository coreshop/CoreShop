/**
 * CoreShop OrderBundle Create Invoice Modal
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect } from 'react'
import { Modal } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { SchemaForm } from '@coreshop/studio-form'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { formatCurrency } from '@coreshop/pimcore/src/utils'

export interface CreateInvoiceModalProps {
  open: boolean
  orderId: number
  currencyCode: string
  onSuccess: () => void
  onCancel: () => void
}

/**
 * Create Invoice Modal
 *
 * Form fields and items grid rendered via SchemaForm.
 * Columns are defined by OrderInvoiceCreationItemsType (configurable via form types).
 */
export const CreateInvoiceModal: React.FC<CreateInvoiceModalProps> = ({
  open,
  orderId,
  currencyCode,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [formData, setFormData] = useState<Record<string, unknown>>({})
  const [loading, setLoading] = useState(false)
  const [loadingItems, setLoadingItems] = useState(false)

  // Load invoiceable items and populate formData
  useEffect(() => {
    if (!open) return

    const loadItems = async () => {
      setLoadingItems(true)
      try {
        const response = await fetch(`/pimcore-studio/api/coreshop/order-invoice/get-invoice-able-items?id=${orderId}`)
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
              quantityInvoiced: item.quantityInvoiced,
              quantity: item.toInvoice,
              maxQuantity: item.maxToInvoice,
            }
          }
          setFormData((prev) => ({ ...prev, items }))
        } else {
          void messageApi.info(t('coreshop_invoice_no_items', { defaultValue: 'No invoiceable items found' }))
          onCancel()
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_invoice_load_items_error', { defaultValue: 'Failed to load invoiceable items' }))))
        onCancel()
      } finally {
        setLoadingItems(false)
      }
    }

    void loadItems()
  }, [open, orderId, onCancel, t])

  // Handle save
  const handleSave = async () => {
    try {
      setLoading(true)

      const itemsObj = (formData.items && typeof formData.items === 'object' && !Array.isArray(formData.items))
        ? formData.items as Record<string, any>
        : {}
      const itemsToInvoice: Record<string, any> = {}
      for (const [key, item] of Object.entries(itemsObj)) {
        if (item.quantity > 0) {
          itemsToInvoice[key] = {
            orderItemId: item.orderItemId,
            quantity: item.quantity,
            maxQuantity: item.maxQuantity
          }
        }
      }

      const { items: _, ...otherFormData } = formData

      const payload = {
        id: orderId,
        items: itemsToInvoice,
        ...otherFormData
      }

      const response = await fetch('/pimcore-studio/api/coreshop/order-invoice/create-invoice', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      const result = await response.json()

      if (result.success) {
        void messageApi.success(t('coreshop_invoice_create_success', { defaultValue: 'Invoice created successfully' }))
        setFormData({})
        onSuccess()
      } else {
        void messageApi.error(renderApiError(result.message || t('coreshop_invoice_create_error', { defaultValue: 'Failed to create invoice' })))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, t('coreshop_invoice_create_error', { defaultValue: 'Failed to create invoice' }))))
    } finally {
      setLoading(false)
    }
  }

  return (
    <Modal
      title={`${t('coreshop_invoice_create_new', { defaultValue: 'Create Invoice for Order' })} (${orderId})`}
      open={open}
      onCancel={onCancel}
      onOk={handleSave}
      okText={t('coreshop_save', { defaultValue: 'Save' })}
      cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      width={1200}
      confirmLoading={loading || loadingItems}
      destroyOnClose
    >
      <SchemaForm
        blockPrefix="coreshop_order_invoice_creation"
        data={formData}
        onChange={(draft) => setFormData((prev) => ({ ...prev, ...draft }))}
      />
    </Modal>
  )
}
