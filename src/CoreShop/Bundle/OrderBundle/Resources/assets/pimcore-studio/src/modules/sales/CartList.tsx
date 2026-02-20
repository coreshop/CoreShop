/**
 * CoreShop OrderBundle Cart List
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
import { Button, Spin } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { BaseListing, DataObjectProvider, listingDefaultProps, type ObjectListingBuilder } from '@pimcore/studio-ui-bundle/modules/data-object'
import { createStyles } from 'antd-style'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { GridToolbar } from '@coreshop/pimcore/src/modules/grid/components/GridToolbar'
import { PresetFilterProvider, usePresetFilter } from '@coreshop/pimcore/src/modules/grid/context/PresetFilterContext'
import { orderCreationApi } from '../order-creation/api'

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

const LIST_TYPE = 'coreshop_cart'

/**
 * Inner component that uses the preset filter context
 */
const CartListInner: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useStyles()
  const [folderId, setFolderId] = React.useState<number | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [listingKey, setListingKey] = React.useState(0)
  const [allowedClasses, setAllowedClasses] = React.useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = React.useState(false)
  const listingBuilder = container.get<ObjectListingBuilder>('CoreShop/Cart/Listing/Builder')

  // Use the preset filter context
  const { selectedFilter, setSelectedFilter } = usePresetFilter()

  React.useEffect(() => {
    const fetchFolderConfig = async (): Promise<void> => {
      try {
        const response = await fetch('/pimcore-studio/api/coreshop/order/get-folder-configuration?saleType=cart')
        const data: FolderConfig = await response.json()

        if (data.success && data.folderId) {
          setFolderId(data.folderId)
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to fetch cart folder configuration')))
      } finally {
        setLoading(false)
      }
    }

    void fetchFolderConfig()
  }, [])

  React.useEffect(() => {
    const loadClasses = async (): Promise<void> => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedClasses(classes)
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load allowed customer classes')))
        setAllowedClasses(['CoreShopCustomer'])
      } finally {
        setClassesLoaded(true)
      }
    }

    void loadClasses()
  }, [messageApi])

  const { open: openCustomerSelector } = useElementSelector({
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
        const customerId = selected.data.id

        void orderCreationApi.getCustomerDetails(customerId).then((details) => {
          const customerName = [details.firstname, details.lastname].filter(Boolean).join(' ') || `Customer #${customerId}`
          const widgetManager = container.get<WidgetRegistry>(serviceIds.widgetManager)

          ;(widgetManager as any).openMainWidget({
            name: `New Order - ${customerName}`,
            id: `coreshop-order-creation-${customerId}`,
            component: 'coreshop-order-creation-detail',
            config: { customerId }
          })
        }).catch((error) => {
          void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer')))
        })
      }
    }
  })

  const handleCreateCart = (): void => {
    if (classesLoaded) {
      openCustomerSelector()
    }
  }

  const handleFilterChange = (filterId: string | null): void => {
    setSelectedFilter(filterId)
    setListingKey(prev => prev + 1)
  }

  const handleRefresh = (): void => {
    setListingKey(prev => prev + 1)
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
        <span>{t('coreshop_no_folder_configured', { defaultValue: 'No folder configured for carts' })}</span>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.toolbar}>
        <GridToolbar
          listType={LIST_TYPE}
          selectedFilter={selectedFilter}
          onFilterChange={handleFilterChange}
          onRefresh={handleRefresh}
        >
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={handleCreateCart}
            disabled={!classesLoaded}
          >
            {t('coreshop_create_cart', { defaultValue: 'Create Cart' })}
          </Button>
        </GridToolbar>
      </div>
      <div className={styles.listing}>
        <DataObjectProvider id={folderId}>
          <BaseListing
            key={listingKey}
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

/**
 * Cart List Component
 *
 * Displays CoreShopCart DataObjects using Pimcore's DataObject listing.
 * Wrapped in PresetFilterProvider to enable filter state sharing.
 */
export const CartList: React.FC = () => {
  return (
    <PresetFilterProvider initialListType={LIST_TYPE}>
      <CartListInner />
    </PresetFilterProvider>
  )
}
