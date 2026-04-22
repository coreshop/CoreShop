/**
 * CoreShop Order Creation Menu Button
 *
 * Opens the Element Selector for customer selection directly from the menu
 * and then opens the Order Creation detail widget for the selected customer.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { store } from '@pimcore/studio-ui-bundle/app'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import { type MenuButtonProps } from '@coreshop/menu/src'
import { orderCreationApi } from '../api'

export const OrderCreationButton = ({ icon, label, closeMainNav }: MenuButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [allowedClasses, setAllowedClasses] = React.useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = React.useState(false)

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

          store.dispatch({
            type: 'widget-manager/openMainWidget',
            payload: {
              name: `New Order - ${customerName}`,
              id: `coreshop-order-creation-${customerId}`,
              component: 'coreshop-order-creation-detail',
              config: { customerId }
            }
          })
        }).catch((error) => {
          void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer')))
        })
      }
    }
  })

  const handleClick = (event: React.MouseEvent): void => {
    event.preventDefault()
    event.stopPropagation()

    if (!classesLoaded) {
      return
    }

    closeMainNav?.()
    openCustomerSelector()
  }

  return (
    <button
      className="main-nav__list-btn"
      onClick={handleClick}
    >
      <Icon value={icon ?? ''} />
      {label || t('coreshop_order_create', { defaultValue: 'Create Order' })}
    </button>
  )
}
