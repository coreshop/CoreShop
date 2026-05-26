/**
 * CoreShop Assign to New Company Button Component
 *
 * Menu button that opens the Element Selector for customer selection,
 * then opens a detail widget tab with the assignment form.
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
import { customerCompanyApi } from '../modules/customer-company-assignment'

export const AssignToNewCompanyButton = ({ icon, label, closeMainNav }: MenuButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [allowedClasses, setAllowedClasses] = React.useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = React.useState(false)

  React.useEffect(() => {
    const loadClasses = async () => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedClasses(classes)
      } catch (err) {
        messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed customer classes')))
        setAllowedClasses(['CoreShopCustomer'])
      } finally {
        setClassesLoaded(true)
      }
    }
    loadClasses()
  }, [])

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

        customerCompanyApi.getEntityDetails('customer', customerId).then((response) => {
          const customerName = response.success && response.data ? response.data.name : `#${customerId}`

          store.dispatch({
            type: 'widget-manager/openMainWidget',
            payload: {
              name: 'Assign to New Company - ' + customerName,
              id: 'coreshop-assign-new-company-' + customerId,
              component: 'coreshop-customer-to-company-assign-to-new-detail',
              config: { customerId },
            },
          })
        }).catch((err) => {
          messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load customer')))
        })
      }
    }
  })

  const handleClick = (e: React.MouseEvent): void => {
    e.preventDefault()
    e.stopPropagation()
    if (classesLoaded) {
      closeMainNav?.()
      openCustomerSelector()
    }
  }

  return (
    <button
      className="main-nav__list-btn"
      onClick={handleClick}
    >
      <Icon value={icon ?? ''} />
      {label || t('coreshop_customer_to_company_assign_to_new', { defaultValue: 'Assign to New Company' })}
    </button>
  )
}
