/**
 * CoreShop Order By Number Modal
 *
 * Opens a modal to search for an order by number, then opens the order detail widget.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { createRoot } from 'react-dom/client'
import { ConfigProvider, Form, Input, message } from 'antd'
import { Modal } from '@pimcore/studio-ui-bundle/components'
import i18next from 'i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { store } from '@pimcore/studio-ui-bundle/app'
import { orderService } from '../services/OrderService'
import { getErrorMessage } from '@coreshop/resource/src/entities'

let modalContainer: HTMLDivElement | null = null
let modalRoot: ReturnType<typeof createRoot> | null = null

const getThemeConfig = (): Record<string, unknown> | undefined => {
  try {
    const themeInstance = container.get<any>(serviceIds['DynamicTypes/Theme/StudioDefaultLight'])
    return themeInstance?.getThemeConfig?.()
  } catch {
    return undefined
  }
}

const openOrderWidget = (id: number, saleNumber: string): void => {
  store.dispatch({
    type: 'widget-manager/openMainWidget',
    payload: {
      name: 'Order #' + saleNumber,
      id: 'coreshop-order-detail' + id,
      component: 'coreshop-order-detail',
      config: {
        orderId: id,
      }
    }
  })
}

const OrderByNumberModal: React.FC<{ onClose: () => void }> = ({ onClose }) => {
  const [open, setOpen] = React.useState(true)
  const [value, setValue] = React.useState('')
  const [loading, setLoading] = React.useState(false)
  const inputRef = React.useRef<any>(null)

  const handleOk = async (): Promise<void> => {
    const trimmed = value.trim()
    if (!trimmed) return

    setLoading(true)
    try {
      const result = await orderService.findOrder(trimmed)

      if (result.success && result.id) {
        openOrderWidget(result.id, result.saleNumber ?? trimmed)
        setOpen(false)
        onClose()
      } else {
        void message.error(i18next.t('element_not_found'))
      }
    } catch (error) {
      void message.error(getErrorMessage(error, i18next.t('error') as string))
    } finally {
      setLoading(false)
    }
  }

  const handleCancel = (): void => {
    setOpen(false)
    onClose()
  }

  return (
    <Modal
      open={open}
      title={i18next.t('coreshop_order_by_number')}
      okText={i18next.t('search')}
      cancelText={i18next.t('cancel')}
      onOk={() => { void handleOk() }}
      onCancel={handleCancel}
      confirmLoading={loading}
      destroyOnClose
      afterOpenChange={(visible) => { if (visible) inputRef.current?.focus() }}
    >
      <Form.Item
        label={i18next.t('coreshop_please_enter_the_number_of_the_order')}
        required
        layout="vertical"
        style={{ marginBottom: 0, marginTop: 16 }}
      >
        <Input
          ref={inputRef}
          value={value}
          onChange={(e) => setValue(e.target.value)}
          onPressEnter={() => { void handleOk() }}
        />
      </Form.Item>
    </Modal>
  )
}

export const openOrderByNumberModal = (): void => {
  if (!modalContainer) {
    modalContainer = document.createElement('div')
    modalContainer.id = 'coreshop-order-by-number-container'
    document.body.appendChild(modalContainer)
    modalRoot = createRoot(modalContainer)
  }

  const cleanup = (): void => {
    if (modalRoot) {
      modalRoot.render(null)
    }
  }

  const themeConfig = getThemeConfig()

  if (modalRoot) {
    modalRoot.render(
      <ConfigProvider theme={themeConfig as any}>
        <OrderByNumberModal onClose={cleanup} />
      </ConfigProvider>
    )
  }
}
