/**
 * CoreShop Assign to Existing Company Button Component
 *
 * Menu button that opens the Element Selector for customer selection,
 * then for company selection, then opens a detail widget tab with the assignment form.
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

let pendingAssignmentCustomer: { id: number, name: string } | null = null

export const AssignToExistingCompanyButton = ({ icon, label }: MenuButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const messageApi = useMessage()
  const [allowedCustomerClasses, setAllowedCustomerClasses] = React.useState<string[]>([])
  const [allowedCompanyClasses, setAllowedCompanyClasses] = React.useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = React.useState(false)
  const selectedCustomerIdRef = React.useRef<number | null>(null)
  const selectedCustomerNameRef = React.useRef<string>('')

  React.useEffect(() => {
    const loadClasses = async () => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const [customerClasses, companyClasses] = await Promise.all([
          configProvider.getAllowedClasses('coreshop.customer'),
          configProvider.getAllowedClasses('coreshop.company'),
        ])
        setAllowedCustomerClasses(customerClasses)
        setAllowedCompanyClasses(companyClasses)
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed classes')))
        setAllowedCustomerClasses(['CoreShopCustomer'])
        setAllowedCompanyClasses(['CoreShopCompany'])
      } finally {
        setClassesLoaded(true)
      }
    }
    void loadClasses()
  }, [messageApi])

  const { open: openCompanySelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedCompanyClasses.length > 0 ? allowedCompanyClasses : undefined
      }
    },
    onFinish: (event) => {
      const selectedCustomerId = selectedCustomerIdRef.current ?? pendingAssignmentCustomer?.id ?? null
      const selectedCustomerName = selectedCustomerNameRef.current || pendingAssignmentCustomer?.name || ''

      if (event.items.length > 0 && selectedCustomerId) {
        const selected = event.items[0]
        const companyId = selected.data.id
        const customerId = selectedCustomerId
        const customerName = selectedCustomerName

        // Open detail widget immediately; company details request is only for nice tab naming.
        store.dispatch({
          type: 'widget-manager/openMainWidget',
          payload: {
            name: 'Assign ' + customerName + ' \u2192 #' + companyId,
            id: 'coreshop-assign-existing-company-' + customerId + '-' + companyId,
            component: 'coreshop-customer-to-company-assign-to-existing-detail',
            config: { customerId, companyId },
          },
        })
        pendingAssignmentCustomer = null

        void customerCompanyApi.getEntityDetails('company', companyId).then((response) => {
          const companyName = response.success && response.data ? response.data.name : `#${companyId}`
          store.dispatch({
            type: 'widget-manager/openMainWidget',
            payload: {
              name: 'Assign ' + customerName + ' \u2192 ' + companyName,
              id: 'coreshop-assign-existing-company-' + customerId + '-' + companyId,
              component: 'coreshop-customer-to-company-assign-to-existing-detail',
              config: { customerId, companyId },
            },
          })
        }).catch(() => {})
      } else if (event.items.length > 0) {
        void messageApi.error(renderApiError('No customer selected before company selection'))
      }
    }
  })

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
        allowedClasses: allowedCustomerClasses.length > 0 ? allowedCustomerClasses : undefined
      }
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        const customerId = selected.data.id

        void customerCompanyApi.getEntityDetails('customer', customerId).then((response) => {
          const customerName = response.success && response.data ? response.data.name : `#${customerId}`
          selectedCustomerIdRef.current = customerId
          selectedCustomerNameRef.current = customerName
          pendingAssignmentCustomer = { id: customerId, name: customerName }
          window.setTimeout(() => {
            openCompanySelector()
          }, 0)
        }).catch((err) => {
          void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load customer')))
        })
      }
    }
  })

  const handleClick = (e: React.MouseEvent): void => {
    e.preventDefault()
    e.stopPropagation()
    if (classesLoaded) {
      openCustomerSelector()
    }
  }

  return (
    <button
      className="main-nav__list-btn"
      onClick={handleClick}
    >
      <Icon value={icon ?? ''} />
      {label || t('coreshop_customer_to_company_assign_to_existing', { defaultValue: 'Assign to Existing Company' })}
    </button>
  )
}
