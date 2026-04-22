/**
 * CoreShop CustomerBundle Customer List
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
import type { ListingBuilder } from '@pimcore/studio-ui-bundle/modules/element'
import { BaseListing, DataObjectProvider, listingDefaultProps } from '@pimcore/studio-ui-bundle/modules/data-object'
import { createStyles } from 'antd-style'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

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
 * Customer List Component
 *
 * Displays CoreShopCustomer DataObjects using Pimcore's DataObject listing
 */
export const CustomerList: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()
  const [folderId, setFolderId] = React.useState<number | null>(null)
  const [loading, setLoading] = React.useState(true)
  const listingBuilder = container.get<ListingBuilder>('CoreShop/Customer/Listing/Builder')

  React.useEffect(() => {
    const fetchFolderConfig = async (): Promise<void> => {
      try {
        const response = await fetch('/pimcore-studio/api/coreshop/customers/folder-configuration')
        const data: FolderConfig = await response.json()

        if (data.success && data.folderId) {
          setFolderId(data.folderId)
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to fetch customer folder configuration')))
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
        <span>{t('coreshop_no_folder_configured', { defaultValue: 'No folder configured for customers' })}</span>
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
