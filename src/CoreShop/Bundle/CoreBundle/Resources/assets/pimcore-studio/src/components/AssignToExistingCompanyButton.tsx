/**
 * CoreShop Assign to Existing Company Nav-Item
 *
 * Opens the Element Selector for customer selection, then for company
 * selection, then opens a detail widget tab with the assignment form.
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

const loadAllowedClasses = async (resource: string, fallback: string): Promise<string[]> => {
  try {
    const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
    const classes = await configProvider.getAllowedClasses(resource)
    return classes.length > 0 ? classes : [fallback]
  } catch {
    return [fallback]
  }
}

const dispatchAssignWidget = (customerId: number, customerName: string, companyId: number, companyLabel: string): void => {
  store.dispatch({
    type: 'widget-manager/openMainWidget',
    payload: {
      name: 'Assign ' + customerName + ' → ' + companyLabel,
      id: 'coreshop-assign-existing-company-' + customerId + '-' + companyId,
      component: 'coreshop-customer-to-company-assign-to-existing-detail',
      config: { customerId, companyId },
    },
  })
}

const openCompanyStep = async (customerId: number, customerName: string): Promise<void> => {
  const allowedCompanyClasses = await loadAllowedClasses('coreshop.company', 'CoreShopCompany')

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
        allowedClasses: allowedCompanyClasses
      }
    },
    onFinish: (event) => {
      if (event.items.length === 0) {
        return
      }

      const companyId = event.items[0].data.id

      dispatchAssignWidget(customerId, customerName, companyId, '#' + companyId)

      customerCompanyApi.getEntityDetails('company', companyId).then((response) => {
        const companyName = response.success && response.data ? response.data.name : `#${companyId}`
        dispatchAssignWidget(customerId, customerName, companyId, companyName)
      }).catch(() => {})
    }
  })
}

const openAssignToExistingCompanyFlow = async (): Promise<void> => {
  const allowedCustomerClasses = await loadAllowedClasses('coreshop.customer', 'CoreShopCustomer')

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
        allowedClasses: allowedCustomerClasses
      }
    },
    onFinish: (event) => {
      if (event.items.length === 0) {
        return
      }

      const customerId = event.items[0].data.id

      customerCompanyApi.getEntityDetails('customer', customerId).then((response) => {
        const customerName = response.success && response.data ? response.data.name : `#${customerId}`
        window.setTimeout(() => { void openCompanyStep(customerId, customerName) }, 0)
      }).catch((err) => {
        void message.error(getErrorMessage(err, 'Failed to load customer'))
      })
    }
  })
}

export const useAssignToExistingCompanyNavItem = (): CustomNavItem => ({
  onClick: () => {
    void openAssignToExistingCompanyFlow()
  }
})
