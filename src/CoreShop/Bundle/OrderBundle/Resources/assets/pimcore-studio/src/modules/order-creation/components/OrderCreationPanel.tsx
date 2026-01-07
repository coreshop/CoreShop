/**
 * CoreShop OrderBundle - Order Creation Panel Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useMemo, useState } from 'react'
import { Button, Space, Alert, Spin, Modal, message, Input, Form, Result } from 'antd'
import {
  ReloadOutlined,
  DeleteOutlined,
  ShoppingCartOutlined,
  FileOutlined,
  CheckCircleOutlined,
  FolderOpenOutlined,
  PlusOutlined,
  CloseOutlined
} from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { createStyles } from 'antd-style'
import { OrderCreationProvider, useOrderCreation } from '../context'
import { CustomerSelector, CustomerInfoCard } from './CustomerSelector'
import type { OrderCreationStepRegistry } from '../registry/OrderCreationStepRegistry'
import { orderCreationServiceIds } from '../service-ids'
import { useSaleHelper } from '../../sales/hooks'

const useStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
    background: ${token.colorBgContainer};
    overflow: hidden;
  `,
  toolbar: css`
    display: flex;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
    flex-shrink: 0;
    background: ${token.colorBgContainer};
  `,
  content: css`
    flex: 1;
    overflow: auto;
    padding: 16px;
  `,
  stepsContainer: css`
    display: flex;
    flex-direction: column;
    gap: 16px;
  `,
  stepWrapper: css`
    /* Step wrapper styles */
  `
}))

const OrderCreationPanelContent: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const { openSale } = useSaleHelper()
  const { state, dispatch, triggerPreview, createSale, reset, fullReset, isValid } =
    useOrderCreation()

  const [cartNameModalOpen, setCartNameModalOpen] = useState(false)
  const [cartName, setCartName] = useState('')
  const [successModal, setSuccessModal] = useState<{
    open: boolean
    type: 'cart' | 'order'
    id: number
  } | null>(null)

  // Get sorted steps from registry
  const stepRegistry = useMemo(() => {
    if (container.isBound(orderCreationServiceIds.stepRegistry)) {
      return container.get<OrderCreationStepRegistry>(orderCreationServiceIds.stepRegistry)
    }
    return null
  }, [])

  const steps = useMemo(() => stepRegistry?.getSorted() ?? [], [stepRegistry])

  // Filter visible steps
  const visibleSteps = useMemo(
    () => steps.filter((step) => !step.isVisible || step.isVisible(state)),
    [steps, state]
  )

  // Generate default cart name
  const getDefaultCartName = (): string => {
    const customer = state.customerDetails
    const date = new Date().toLocaleDateString()
    if (customer) {
      return `${customer.firstname ?? ''} ${customer.lastname ?? ''} - ${date}`.trim()
    }
    return date
  }

  const handleCreateCart = (): void => {
    setCartName(getDefaultCartName())
    setCartNameModalOpen(true)
  }

  const handleConfirmCreateCart = async (): Promise<void> => {
    setCartNameModalOpen(false)
    const id = await createSale('cart', cartName)
    if (id) {
      setSuccessModal({ open: true, type: 'cart', id })
    }
    setCartName('')
  }

  const handleCreateOrder = async (): Promise<void> => {
    const id = await createSale('order')
    if (id) {
      setSuccessModal({ open: true, type: 'order', id })
    }
  }

  const handleSuccessClose = (): void => {
    setSuccessModal(null)
    fullReset()
  }

  const handleSuccessCreateAnother = (): void => {
    setSuccessModal(null)
    reset()
  }

  const handleSuccessOpen = (): void => {
    if (successModal) {
      openSale({ id: successModal.id, type: successModal.type })
    }
    setSuccessModal(null)
    reset()
  }

  const handleChangeCustomer = (): void => {
    fullReset()
  }

  // Show customer selector if no customer
  if (!state.customerId || !state.customerDetails) {
    return <CustomerSelector />
  }

  return (
    <div className={styles.container}>
      {/* Toolbar - fixed at top */}
      <div className={styles.toolbar}>
        <Space>
          <Button icon={<DeleteOutlined />} onClick={reset} disabled={state.creating}>
            {t('coreshop_reset', { defaultValue: 'Reset' })}
          </Button>
          <Button icon={<ReloadOutlined />} onClick={triggerPreview} disabled={state.creating}>
            {t('coreshop_refresh', { defaultValue: 'Refresh' })}
          </Button>
        </Space>
        <Space>
          <Button
            type="primary"
            icon={<ShoppingCartOutlined />}
            onClick={handleCreateCart}
            disabled={!isValid() || state.creating}
            loading={state.creating}
          >
            {t('coreshop_create_cart', { defaultValue: 'Create Cart' })}
          </Button>
          <Button
            type="primary"
            icon={<FileOutlined />}
            onClick={() => void handleCreateOrder()}
            disabled={!isValid() || state.creating}
            loading={state.creating}
          >
            {t('coreshop_create_order', { defaultValue: 'Create Order' })}
          </Button>
        </Space>
      </div>

      {/* Content - scrollable */}
      <div className={styles.content}>
        <Spin
          spinning={state.creating}
          tip={t('coreshop_order_creation_creating', { defaultValue: 'Creating...' })}
          size="large"
        >
        {/* Customer Info */}
        <CustomerInfoCard
          customer={state.customerDetails}
          onChangeCustomer={handleChangeCustomer}
        />

        {/* Error display */}
        {state.previewError && (
          <Alert
            type="error"
            message={state.previewError}
            showIcon
            style={{ marginBottom: 16 }}
            closable
          />
        )}

        {state.createError && (
          <Alert
            type="error"
            message={state.createError}
            showIcon
            style={{ marginBottom: 16 }}
            closable
          />
        )}

        {/* Steps */}
        <Spin spinning={state.previewLoading}>
          <div className={styles.stepsContainer}>
            {visibleSteps.map((step) => {
              const StepComponent = step.component
              return (
                <div key={step.key} className={styles.stepWrapper}>
                  <StepComponent
                    state={state}
                    dispatch={dispatch}
                    preview={state.preview}
                    triggerPreview={triggerPreview}
                  />
                </div>
              )
            })}
          </div>
        </Spin>
        </Spin>
      </div>

      {/* Cart Name Modal */}
      <Modal
        title={t('coreshop_create_cart', { defaultValue: 'Create Cart' })}
        open={cartNameModalOpen}
        onOk={() => void handleConfirmCreateCart()}
        onCancel={() => setCartNameModalOpen(false)}
        okText={t('coreshop_create', { defaultValue: 'Create' })}
        cancelText={t('coreshop_cancel', { defaultValue: 'Cancel' })}
      >
        <Form layout="vertical">
          <Form.Item
            label={t('coreshop_cart_name', { defaultValue: 'Cart Name' })}
          >
            <Input
              value={cartName}
              onChange={(e) => setCartName(e.target.value)}
              placeholder={t('coreshop_cart_name_placeholder', {
                defaultValue: 'Enter a name for this cart'
              })}
              autoFocus
            />
          </Form.Item>
        </Form>
      </Modal>

      {/* Success Modal */}
      <Modal
        open={successModal?.open ?? false}
        onCancel={handleSuccessClose}
        footer={null}
        centered
        width={480}
      >
        <Result
          icon={<CheckCircleOutlined style={{ color: '#52c41a' }} />}
          title={
            successModal?.type === 'cart'
              ? t('coreshop_order_creation_cart_created', { defaultValue: 'Cart Created Successfully' })
              : t('coreshop_order_creation_order_created', { defaultValue: 'Order Created Successfully' })
          }
          subTitle={
            successModal?.type === 'cart'
              ? t('coreshop_order_creation_cart_created_description', {
                  defaultValue: 'The cart has been saved and is ready for further processing.'
                })
              : t('coreshop_order_creation_order_created_description', {
                  defaultValue: 'The order has been created and confirmed.'
                })
          }
          extra={
            <Space direction="vertical" size="middle" style={{ width: '100%' }}>
              <Button
                type="primary"
                icon={<FolderOpenOutlined />}
                onClick={handleSuccessOpen}
                block
                size="large"
              >
                {successModal?.type === 'cart'
                  ? t('coreshop_order_creation_open_cart', { defaultValue: 'Open Cart' })
                  : t('coreshop_order_creation_open_order', { defaultValue: 'Open Order' })}
              </Button>
              <Button
                icon={<PlusOutlined />}
                onClick={handleSuccessCreateAnother}
                block
              >
                {t('coreshop_order_creation_create_another_same_customer', {
                  defaultValue: 'Create Another for Same Customer'
                })}
              </Button>
              <Button
                icon={<CloseOutlined />}
                onClick={handleSuccessClose}
                block
              >
                {t('coreshop_close', { defaultValue: 'Close' })}
              </Button>
            </Space>
          }
        />
      </Modal>
    </div>
  )
}

// Main exported component
export const OrderCreationPanel: React.FC = () => {
  return (
    <OrderCreationProvider>
      <OrderCreationPanelContent />
    </OrderCreationProvider>
  )
}
