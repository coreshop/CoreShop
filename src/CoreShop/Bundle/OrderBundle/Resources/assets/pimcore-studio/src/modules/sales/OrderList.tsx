/**
 * CoreShop OrderBundle Order List
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
import { Button, Space, Spin } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { BaseListing, DataObjectProvider, listingDefaultProps, type ObjectListingBuilder } from '@pimcore/studio-ui-bundle/modules/data-object'
import { createStyles } from 'antd-style'
import { getErrorMessage } from '@coreshop/resource/src/entities'

const useStyles = createStyles(({ css }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
  `,
  toolbar: css`
    padding: 8px 16px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
  `,
  listing: css`
    flex: 1;
    overflow: hidden;
  `,
  loading: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `
}))

interface FolderConfig {
  success: boolean
  className: string
  folderId: number
}

/**
 * Order List Component
 *
 * Displays CoreShopOrder DataObjects using Pimcore's DataObject listing
 * Based on: https://github.com/pimcore/studio-example-bundle/blob/main/assets/js/src/examples/listings/components/custom-listing.tsx
 */
export const OrderList: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useStyles()
  const [folderId, setFolderId] = React.useState<number | null>(null)
  const [loading, setLoading] = React.useState(true)
  const listingBuilder = container.get<ObjectListingBuilder>('CoreShop/Order/Listing/Builder')

  React.useEffect(() => {
    const fetchFolderConfig = async (): Promise<void> => {
      try {
        const response = await fetch('/pimcore-studio/api/coreshop/order/get-folder-configuration?saleType=order')
        const data: FolderConfig = await response.json()

        if (data.success && data.folderId) {
          setFolderId(data.folderId)
        }
      } catch (error) {
        void messageApi.error(getErrorMessage(error, 'Failed to fetch order folder configuration'))
      } finally {
        setLoading(false)
      }
    }

    void fetchFolderConfig()
  }, [])

  const handleCreateOrder = (): void => {
    const widgetManager = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetManager.openWidget({
      name: 'coreshop-order-creation',
      config: {}
    })
  }

  if (loading) {
    return (
      <div className={styles.loading}>
        <Spin size="large" />
      </div>
    )
  }

  if (folderId === null) {
    return (
      <div className={styles.loading}>
        <span>{t('coreshop_no_folder_configured', { defaultValue: 'No folder configured for orders' })}</span>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.toolbar}>
        <Space>
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={handleCreateOrder}
          >
            {t('coreshop_create_order', { defaultValue: 'Create Order' })}
          </Button>
        </Space>
      </div>
      <div className={styles.listing}>
        <DataObjectProvider id={folderId}>
          <BaseListing
            {...listingBuilder.build({
              props: {
                ...listingDefaultProps
              },
              config: {}
            })}
          />
        </DataObjectProvider>
      </div>
    </div>
  )
}
