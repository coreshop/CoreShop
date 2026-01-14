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
import { getErrorMessage } from '@coreshop/resource/src/entities'

const useStyles = createStyles(({ css }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
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
 * Quote List Component
 *
 * Displays CoreShopQuote DataObjects using Pimcore's DataObject listing
 */
export const QuoteList: React.FC = () => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const { styles } = useStyles()
  const [folderId, setFolderId] = React.useState<number | null>(null)
  const [loading, setLoading] = React.useState(true)
  const listingBuilder = container.get<ObjectListingBuilder>('CoreShop/Quote/Listing/Builder')

  React.useEffect(() => {
    const fetchFolderConfig = async (): Promise<void> => {
      try {
        const response = await fetch('/pimcore-studio/api/coreshop/order/get-folder-configuration?saleType=quote')
        const data: FolderConfig = await response.json()

        if (data.success && data.folderId) {
          setFolderId(data.folderId)
        }
      } catch (error) {
        void messageApi.error(getErrorMessage(error, 'Failed to fetch quote folder configuration'))
      } finally {
        setLoading(false)
      }
    }

    void fetchFolderConfig()
  }, [])

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
