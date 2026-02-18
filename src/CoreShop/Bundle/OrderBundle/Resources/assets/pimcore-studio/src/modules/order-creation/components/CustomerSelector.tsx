/**
 * CoreShop OrderBundle - Customer Selector Component
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
import { Card, Button, Space, Typography, Spin, Alert, Avatar } from 'antd'
import { UserOutlined, SearchOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { useOrderCreation } from '../context'
import type { CustomerDetails } from '../types'

const useStyles = createStyles(({ css }) => ({
  selectorCard: css`
    max-width: 600px;
    margin: 0 auto;
  `,
  selectButton: css`
    min-width: 200px;
  `,
}))

export const CustomerSelector: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()
  const { loadCustomer } = useOrderCreation()
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  // Load allowed customer classes from config
  useEffect(() => {
    const loadAllowedClasses = async () => {
      try {
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedClasses(classes)
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed customer classes')))
        // Fallback to default
        setAllowedClasses(['CoreShopCustomer'])
      }
    }
    void loadAllowedClasses()
  }, [configProvider, messageApi])

  const handleCustomerSelected = useCallback(async (customerId: number) => {
    setLoading(true)
    setError(null)
    try {
      await loadCustomer(customerId)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load customer')
    } finally {
      setLoading(false)
    }
  }, [loadCustomer])

  const { open: openSelector } = useElementSelector({
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
        void handleCustomerSelected(selected.data.id)
      }
    }
  })

  return (
    <div style={{ padding: 24 }}>
      <Card className={styles.selectorCard}>
        <Space direction="vertical" size="large" style={{ width: '100%' }}>
          <div style={{ textAlign: 'center' }}>
            <UserOutlined style={{ fontSize: 48, color: '#1890ff', marginBottom: 16 }} />

            <Typography.Title level={4} style={{ margin: 0 }}>
              {t('coreshop_order_creation_select_customer', { defaultValue: 'Select a Customer' })}
            </Typography.Title>

            <Typography.Text type="secondary">
              {t('coreshop_order_creation_select_customer_description', {
                defaultValue: 'Select a customer to create an order for.'
              })}
            </Typography.Text>
          </div>

          {error && (
            <Alert type="error" message={error} showIcon closable onClose={() => setError(null)} />
          )}

          <Spin spinning={loading}>
            <div style={{ textAlign: 'center' }}>
              <Button
                type="primary"
                size="large"
                icon={<SearchOutlined />}
                onClick={openSelector}
                className={styles.selectButton}
              >
                {t('coreshop_order_creation_browse_customers', { defaultValue: 'Browse Customers' })}
              </Button>
            </div>
          </Spin>
        </Space>
      </Card>
    </div>
  )
}

interface CustomerInfoCardProps {
  customer: CustomerDetails
  onChangeCustomer: () => void
}

const useInfoCardStyles = createStyles(({ css, token }) => ({
  customerRow: css`
    display: flex;
    align-items: center;
    gap: 12px;
  `,
  avatar: css`
    flex-shrink: 0;
    background: ${token.colorPrimary};
    font-weight: 600;
  `,
  info: css`
    flex: 1;
    min-width: 0;
  `,
  name: css`
    font-weight: 600;
    font-size: 14px;
    line-height: 1.3;
  `,
  email: css`
    font-size: 12px;
    color: ${token.colorTextSecondary};
    line-height: 1.3;
  `
}))

export const CustomerInfoCard: React.FC<CustomerInfoCardProps> = ({
  customer,
  onChangeCustomer
}) => {
  const { t } = useTranslation()
  const { styles } = useInfoCardStyles()

  const initials = [customer.firstname, customer.lastname]
    .filter(Boolean)
    .map(n => n!.charAt(0).toUpperCase())
    .join('')

  return (
    <Card
      title={t('coreshop_customer', { defaultValue: 'Customer' })}
      extra={
        <Button size="small" onClick={onChangeCustomer}>
          {t('coreshop_order_creation_change_customer', { defaultValue: 'Change' })}
        </Button>
      }
    >
      <div className={styles.customerRow}>
        <Avatar size={40} className={styles.avatar}>
          {initials || <UserOutlined />}
        </Avatar>
        <div className={styles.info}>
          <div className={styles.name}>
            {customer.firstname} {customer.lastname}
          </div>
          {customer.email && (
            <div className={styles.email}>{customer.email}</div>
          )}
        </div>
      </div>
    </Card>
  )
}
