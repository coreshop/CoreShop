/**
 * CoreShop Assign to New Company Nav-Item
 *
 * Opens the Element Selector for customer selection and, after selection,
 * opens a detail widget tab with the assignment form.
 *
 * The factory intentionally avoids React hooks — Pimcore calls it via an
 * IIFE during MainNav render, so any hooks inside would be attributed to
 * MainNav and break when menu items are added asynchronously (React #310).
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { message } from 'antd'
import { container } from '@pimcore/studio-ui-bundle'
import { store, getPimcoreStudioApi } from '@pimcore/studio-ui-bundle/app'
import { SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { getErrorMessage } from '@coreshop/resource/src/entities'
import type { CustomNavItem } from '@coreshop/menu/src'
import { customerCompanyApi } from '../modules/customer-company-assignment'

const loadAllowedCustomerClasses = async (): Promise<string[]> => {
  try {
    const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
    const classes = await configProvider.getAllowedClasses('coreshop.customer')
    return classes.length > 0 ? classes : ['CoreShopCustomer']
  } catch {
    return ['CoreShopCustomer']
  }
}

const openAssignToNewCompanyFlow = async (): Promise<void> => {
  const allowedClasses = await loadAllowedCustomerClasses()

  getPimcoreStudioApi().element.openElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses
      }
    },
    onFinish: (event) => {
      if (event.items.length === 0) {
        return
      }

      const customerId = event.items[0].data.id

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
      }).catch((error) => {
        void message.error(getErrorMessage(error, 'Failed to load customer'))
      })
    }
  })
}

export const useAssignToNewCompanyNavItem = (): CustomNavItem => ({
  onClick: () => {
    void openAssignToNewCompanyFlow()
  }
})
