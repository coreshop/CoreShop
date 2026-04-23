/**
 * CoreShop OrderBundle Quote List
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
import { Spin } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { BaseListing, DataObjectProvider, listingDefaultProps, type ObjectListingBuilder } from '@pimcore/studio-ui-bundle/modules/data-object'
import { createStyles } from 'antd-style'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { GridToolbar } from '@coreshop/pimcore/src/modules/grid/components/GridToolbar'
import { PresetFilterProvider, usePresetFilter } from '@coreshop/pimcore/src/modules/grid/context/PresetFilterContext'

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

const LIST_TYPE = 'coreshop_quote'

/**
 * Inner component that uses the preset filter context
 */
const QuoteListInner: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useStyles()
  const [folderId, setFolderId] = React.useState<number | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [listingKey, setListingKey] = React.useState(0)
  const listingBuilder = container.get<ObjectListingBuilder>('CoreShop/Quote/Listing/Builder')

  // Use the preset filter context
  const { selectedFilter, setSelectedFilter } = usePresetFilter()

  React.useEffect(() => {
    const fetchFolderConfig = async (): Promise<void> => {
      try {
        const response = await fetch('/pimcore-studio/api/coreshop/order/get-folder-configuration?saleType=quote')
        const data: FolderConfig = await response.json()

        if (data.success && data.folderId) {
          setFolderId(data.folderId)
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to fetch quote folder configuration')))
      } finally {
        setLoading(false)
      }
    }

    void fetchFolderConfig()
  }, [])

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
        <span>{t('coreshop_no_folder_configured', { defaultValue: 'No folder configured for quotes' })}</span>
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
        />
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
 * Quote List Component
 *
 * Displays CoreShopQuote DataObjects using Pimcore's DataObject listing.
 * Wrapped in PresetFilterProvider to enable filter state sharing.
 */
export const QuoteList: React.FC = () => {
  return (
    <PresetFilterProvider initialListType={LIST_TYPE}>
      <QuoteListInner />
    </PresetFilterProvider>
  )
}
