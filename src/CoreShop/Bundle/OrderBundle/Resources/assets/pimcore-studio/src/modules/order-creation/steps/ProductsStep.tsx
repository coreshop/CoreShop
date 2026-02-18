/**
 * CoreShop OrderBundle - Products Step Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useState, useEffect, useMemo, useCallback } from 'react'
import { Card, Table, Button, InputNumber, Typography, Popconfirm, Space } from 'antd'
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useTableCardStyles } from '../../sales/styles/useTableCardStyles'
import type {
  OrderCreationStepConfig,
  OrderCreationState,
  OrderCreationStepProps,
  OrderCreationItem,
  PreviewItem
} from '../types'

const useStyles = createStyles(({ css, token }) => ({
  emptyState: css`
    padding: 32px;
    text-align: center;
    background: ${token.colorBgLayout};
    border-radius: ${token.borderRadius}px;
  `,
  addButton: css`
    margin-top: 16px;
  `,
}))

/**
 * Format currency value (cents to display)
 */
const formatCurrency = (value: number, isoCode?: string): string => {
  const amount = value / 100
  if (isoCode) {
    try {
      return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: isoCode
      }).format(amount)
    } catch {
      // Fallback if currency code is invalid
    }
  }
  return amount.toFixed(2)
}

const ProductsStepComponent: React.FC<OrderCreationStepProps> = ({ state, dispatch, triggerPreview }) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const { styles: tableStyles } = useTableCardStyles()
  const messageApi = useMessage()
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  // Load allowed purchasable classes from config
  useEffect(() => {
    const loadAllowedClasses = async () => {
      try {
        const classes = await configProvider.getAllowedClasses('coreshop.purchasable')
        setAllowedClasses(classes)
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed purchasable classes')))
        // Fallback to default
        setAllowedClasses(['CoreShopProduct'])
      }
    }
    void loadAllowedClasses()
  }, [configProvider, messageApi])

  const handleAddProducts = useCallback((productIds: number[]): void => {
    if (productIds.length === 0) return

    // Filter out products that are already in the list
    const existingProductIds = new Set(state.formData.items.map(item => item.product))
    const newProductIds = productIds.filter(id => !existingProductIds.has(id))

    if (newProductIds.length === 0) return

    const newItems: OrderCreationItem[] = newProductIds.map((productId) => ({
      product: productId,
      quantity: 1,
      customItemPrice: 0,
      customItemDiscount: 0
    }))

    dispatch({
      type: 'UPDATE_FORM_DATA',
      payload: {
        items: [...state.formData.items, ...newItems]
      }
    })

    triggerPreview()
  }, [state.formData.items, dispatch, triggerPreview])

  const { open: openProductSelector } = useElementSelector({
    selectionType: SelectionType.Multiple,
    areas: {
      asset: false,
      document: false,
      object: true
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedClasses.length > 0 ? allowedClasses : undefined
      }
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const productIds = event.items.map(item => item.data.id)
        handleAddProducts(productIds)
      }
    }
  })

  const handleQuantityChange = (index: number, quantity: number | null): void => {
    if (quantity === null || quantity < 1) return

    const newItems = [...state.formData.items]
    newItems[index] = { ...newItems[index], quantity }
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { items: newItems } })
    triggerPreview()
  }

  const handleCustomPriceChange = (index: number, value: number | null): void => {
    const newItems = [...state.formData.items]
    // Convert from display value to cents
    newItems[index] = { ...newItems[index], customItemPrice: (value ?? 0) * 100 }
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { items: newItems } })
    triggerPreview()
  }

  const handleCustomDiscountChange = (index: number, value: number | null): void => {
    const newItems = [...state.formData.items]
    // Convert from display value to cents
    newItems[index] = { ...newItems[index], customItemDiscount: (value ?? 0) * 100 }
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { items: newItems } })
    triggerPreview()
  }

  const handleRemoveProduct = (index: number): void => {
    const newItems = state.formData.items.filter((_, i) => i !== index)
    dispatch({ type: 'UPDATE_FORM_DATA', payload: { items: newItems } })
    triggerPreview()
  }

  // Use preview data for display
  const previewItems = state.preview?.items || []
  const currencyCode = state.preview?.baseCurrency?.isoCode

  const columns = [
    {
      title: t('coreshop_id', { defaultValue: 'ID' }),
      dataIndex: 'product',
      width: 80
    },
    {
      title: t('coreshop_name', { defaultValue: 'Name' }),
      dataIndex: 'productName',
      render: (_: unknown, record: OrderCreationItem, index: number) => {
        const previewItem = previewItems[index] as PreviewItem | undefined
        return previewItem?.productName || `Product #${record.product}`
      }
    },
    {
      title: t('coreshop_quantity', { defaultValue: 'Quantity' }),
      dataIndex: 'quantity',
      width: 100,
      render: (value: number, _: OrderCreationItem, index: number) => (
        <InputNumber
          min={1}
          value={value}
          onChange={(val) => handleQuantityChange(index, val)}
          size="small"
          style={{ width: '100%' }}
        />
      )
    },
    {
      title: t('coreshop_custom_price', { defaultValue: 'Custom Price' }),
      dataIndex: 'customItemPrice',
      width: 120,
      render: (value: number | undefined, _: OrderCreationItem, index: number) => (
        <InputNumber
          min={0}
          value={(value ?? 0) / 100}
          onChange={(val) => handleCustomPriceChange(index, val)}
          size="small"
          style={{ width: '100%' }}
          precision={2}
          step={0.01}
        />
      )
    },
    {
      title: t('coreshop_custom_discount', { defaultValue: 'Discount' }),
      dataIndex: 'customItemDiscount',
      width: 120,
      render: (value: number | undefined, _: OrderCreationItem, index: number) => (
        <InputNumber
          min={0}
          value={(value ?? 0) / 100}
          onChange={(val) => handleCustomDiscountChange(index, val)}
          size="small"
          style={{ width: '100%' }}
          precision={2}
          step={0.01}
        />
      )
    },
    {
      title: t('coreshop_unit_price', { defaultValue: 'Unit Price' }),
      dataIndex: 'price',
      width: 120,
      align: 'right' as const,
      render: (_: unknown, __: OrderCreationItem, index: number) => {
        const previewItem = previewItems[index] as PreviewItem | undefined
        if (!previewItem) return '-'
        return formatCurrency(previewItem.price, currencyCode)
      }
    },
    {
      title: t('coreshop_total', { defaultValue: 'Total' }),
      dataIndex: 'total',
      width: 120,
      align: 'right' as const,
      render: (_: unknown, __: OrderCreationItem, index: number) => {
        const previewItem = previewItems[index] as PreviewItem | undefined
        if (!previewItem) return '-'
        return (
          <Typography.Text strong>
            {formatCurrency(previewItem.total, currencyCode)}
          </Typography.Text>
        )
      }
    },
    {
      title: '',
      width: 50,
      render: (_: unknown, __: OrderCreationItem, index: number) => (
        <Popconfirm
          title={t('coreshop_remove_product_confirm', {
            defaultValue: 'Remove this product?'
          })}
          onConfirm={() => handleRemoveProduct(index)}
          okText={t('coreshop_yes', { defaultValue: 'Yes' })}
          cancelText={t('coreshop_no', { defaultValue: 'No' })}
        >
          <Button type="text" danger icon={<DeleteOutlined />} size="small" />
        </Popconfirm>
      )
    }
  ]

  return (
    <Card
      title={t('coreshop_order_creation_products', { defaultValue: 'Products' })}
      className={tableStyles.card}
      extra={
        state.formData.items.length > 0 && (
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={openProductSelector}
            size="small"
          >
            {t('coreshop_order_creation_add_products', { defaultValue: 'Add Products' })}
          </Button>
        )
      }
    >
      {state.formData.items.length > 0 ? (
        <Table
          dataSource={state.formData.items}
          columns={columns}
          rowKey={(_, index) => `item-${index}`}
          pagination={false}
          size="small"
          className={tableStyles.table}
          scroll={{ x: 'max-content' }}
        />
      ) : (
        <div className={styles.emptyState}>
          <Space direction="vertical" size="middle">
            <Typography.Text type="secondary">
              {t('coreshop_order_creation_no_products', {
                defaultValue: 'No products added yet. Click the button below to add products.'
              })}
            </Typography.Text>
            <Button
              type="primary"
              icon={<PlusOutlined />}
              onClick={openProductSelector}
            >
              {t('coreshop_order_creation_add_products', { defaultValue: 'Add Products' })}
            </Button>
          </Space>
        </div>
      )}
    </Card>
  )
}

export const ProductsStepConfig: OrderCreationStepConfig = {
  key: 'products',
  label: 'coreshop_order_creation_products',
  icon: 'coreshop_icon_cart',
  priority: 30,
  component: ProductsStepComponent,

  isValid: (state: OrderCreationState) => {
    return state.formData.items.length > 0
  },

  getValues: (state: OrderCreationState) => ({
    items: state.formData.items
  })
}
