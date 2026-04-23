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

import React, { useMemo, useState, useCallback, useEffect } from 'react'
import { Button, Space, Alert, Spin, Modal, Input, Form, Result } from 'antd'
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
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { OrderCreationProvider, useOrderCreation } from '../context'
import { CustomerInfoCard } from './CustomerSelector'
import type { OrderCreationStepRegistry } from '../registry/OrderCreationStepRegistry'
import { orderCreationServiceIds } from '../service-ids'
import { orderCreationApi } from '../api'
import { useSaleHelper } from '../../sales/hooks'

const useStyles = createStyles(({ css, token }) => ({
  container: css`
    container-type: inline-size;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 16px;
    background: ${token.colorBgLayout};
    gap: 16px;
  `,
  toolbar: css`
    display: flex;
    align-items: center;
    gap: 8px;
    background: ${token.colorBgContainer};
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: ${token.borderRadiusLG}px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    padding: 8px;
    position: sticky;
    top: 0;
    z-index: 10;
  `,
  toolbarSpacer: css`
    flex: 1;
  `,
  content: css`
    display: flex;
    flex-direction: column;
    gap: 16px;
  `,
  topRow: css`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 700px) {
      flex-direction: row;
      align-items: stretch;

      > :first-child {
        flex: 0 0 auto;
        min-width: 280px;
        max-width: 340px;
      }

      > :last-child {
        flex: 1;
        min-width: 0;
      }

      > * > .ant-card {
        height: 100%;
      }
    }
  `,
  multiRow: css`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;

      > * {
        flex: 1;
        min-width: 0;
      }

      > * > .ant-card {
        height: 100%;
      }
    }
  `,
  block: css`
    .ant-card {
      border-radius: ${token.borderRadiusLG}px;
      border: 1px solid ${token.colorBorderSecondary};
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02);
      overflow: hidden;

      .ant-card-head {
        border-bottom: 1px solid ${token.colorBorderSecondary};
        min-height: 44px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        color: ${token.colorTextSecondary};
      }

      .ant-card-head-title {
        font-size: 13px;
        padding: 10px 0;
      }

      .ant-card-extra {
        padding: 6px 0;
      }

      .ant-card-body {
        > div:last-child {
          margin-bottom: 0;
        }

        .ant-form-item:last-child {
          margin-bottom: 0;
        }

        .ant-row:last-child .ant-form-item {
          margin-bottom: 0;
        }
      }
    }
  `,
  blockDisabled: css`
    position: relative;
    pointer-events: none;

    &::after {
      content: '';
      position: absolute;
      inset: 0;
      background: ${token.colorBgContainer};
      opacity: 0.55;
      z-index: 1;
      border-radius: ${token.borderRadiusLG}px;
    }
  `
}))

const OrderCreationPanelContent: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const { openSale } = useSaleHelper()
  const widgetManager = useWidgetManager()
  const messageApi = useMessage()
  const { state, dispatch, triggerPreview, createSale, reset, fullReset, isValid } =
    useOrderCreation()

  const [cartNameModalOpen, setCartNameModalOpen] = useState(false)
  const [cartName, setCartName] = useState('')
  const [successModal, setSuccessModal] = useState<{
    open: boolean
    type: 'cart' | 'order'
    id: number
  } | null>(null)
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const load = async () => {
      try {
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedClasses(classes)
      } catch {
        setAllowedClasses(['CoreShopCustomer'])
      }
    }
    void load()
  }, [configProvider])

  const handleNewCustomerSelected = useCallback(async (customerId: number) => {
    try {
      const details = await orderCreationApi.getCustomerDetails(customerId)
      const customerName = [details.firstname, details.lastname].filter(Boolean).join(' ') || `Customer #${customerId}`

      widgetManager.openMainWidget({
        name: `New Order - ${customerName}`,
        id: `coreshop-order-creation-${customerId}`,
        component: 'coreshop-order-creation-detail',
        config: {
          customerId
        }
      })
    } catch (err) {
      void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load customer')))
    }
  }, [widgetManager, messageApi])

  const { open: openChangeSelector } = useElementSelector({
    selectionType: SelectionType.Single,
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
        const selected = event.items[0]
        void handleNewCustomerSelected(selected.data.id)
      }
    }
  })

  // Get sorted steps from registry
  const stepRegistry = useMemo(() => {
    if (container.isBound(orderCreationServiceIds.stepRegistry)) {
      return container.get<OrderCreationStepRegistry>(orderCreationServiceIds.stepRegistry)
    }
    return null
  }, [])

  const steps = useMemo(() => stepRegistry?.getSorted() ?? [], [stepRegistry])

  // Determine which steps are disabled (not yet available)
  const stepDisabled = useMemo(
    () => new Map(steps.map((step) => [step.key, step.isVisible ? !step.isVisible(state) : false])),
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
    openChangeSelector()
  }

  // Show loading state while customer is being loaded
  if (!state.customerId || !state.customerDetails) {
    return (
      <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%' }}>
        <Spin size="large" />
      </div>
    )
  }

  return (
    <div className={styles.container}>
      {/* Toolbar - floating sticky */}
      <div className={styles.toolbar}>
        <Button icon={<DeleteOutlined />} onClick={reset} disabled={state.creating} size="small">
          {t('coreshop_reset', { defaultValue: 'Reset' })}
        </Button>
        <Button icon={<ReloadOutlined />} onClick={triggerPreview} disabled={state.creating} size="small">
          {t('coreshop_refresh', { defaultValue: 'Refresh' })}
        </Button>
        <div className={styles.toolbarSpacer} />
        <Button
          type="primary"
          icon={<ShoppingCartOutlined />}
          onClick={handleCreateCart}
          disabled={!isValid() || state.creating}
          loading={state.creating}
          size="small"
        >
          {t('coreshop_create_cart', { defaultValue: 'Create Cart' })}
        </Button>
        <Button
          type="primary"
          icon={<FileOutlined />}
          onClick={() => void handleCreateOrder()}
          disabled={!isValid() || state.creating}
          loading={state.creating}
          size="small"
        >
          {t('coreshop_create_order', { defaultValue: 'Create Order' })}
        </Button>
      </div>

      {/* Content */}
      <Spin
        spinning={state.creating}
        tip={t('coreshop_order_creation_creating', { defaultValue: 'Creating...' })}
        size="large"
      >
        <div key={state.resetKey} className={styles.content}>
          {/* Customer + Base Settings side by side */}
          <div className={styles.topRow}>
            <div className={styles.block}>
              <CustomerInfoCard
                customer={state.customerDetails}
                onChangeCustomer={handleChangeCustomer}
              />
            </div>
            {steps.filter(s => s.key === 'base').map((step) => {
              const StepComponent = step.component
              return (
                <div key={step.key} className={styles.block}>
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

          {/* Error display */}
          {state.previewError && (
            <Alert
              type="error"
              message={state.previewError}
              showIcon
              closable
            />
          )}

          {state.createError && (
            <Alert
              type="error"
              message={state.createError}
              showIcon
              closable
            />
          )}

          {/* Steps (excluding base, rendered above) */}
          <Spin spinning={state.previewLoading}>
            {(() => {
              const middleKeys = new Set(['address', 'shipping', 'payment'])
              const skipKeys = new Set(['base', ...middleKeys])
              const beforeMiddle = steps.filter(s => !skipKeys.has(s.key) && s.priority < 40)
              const middleSteps = steps.filter(s => middleKeys.has(s.key))
              const afterMiddle = steps.filter(s => !skipKeys.has(s.key) && s.priority >= 40)

              const renderStep = (step: typeof steps[0]) => {
                const disabled = stepDisabled.get(step.key) ?? false
                const StepComponent = step.component
                return (
                  <div key={step.key} className={`${styles.block} ${disabled ? styles.blockDisabled : ''}`}>
                    <StepComponent
                      state={state}
                      dispatch={dispatch}
                      preview={state.preview}
                      triggerPreview={triggerPreview}
                    />
                  </div>
                )
              }

              return (
                <div className={styles.content}>
                  {/* Steps before the middle group (e.g. products) */}
                  {beforeMiddle.map(renderStep)}

                  {/* Address + Shipping + Payment side by side */}
                  {middleSteps.length > 0 && (
                    <div className={styles.multiRow}>
                      {middleSteps.map(renderStep)}
                    </div>
                  )}

                  {/* Steps after middle group (e.g. totals) */}
                  {afterMiddle.map(renderStep)}
                </div>
              )
            })()}
          </Spin>
        </div>
      </Spin>

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

interface OrderCreationPanelProps {
  customerId?: number
}

// Main exported component - receives customerId from widget config
export const OrderCreationPanel: React.FC<OrderCreationPanelProps> = (config) => {
  const customerId = config?.customerId

  return (
    <OrderCreationProvider initialCustomerId={customerId}>
      <OrderCreationPanelContent />
    </OrderCreationProvider>
  )
}
