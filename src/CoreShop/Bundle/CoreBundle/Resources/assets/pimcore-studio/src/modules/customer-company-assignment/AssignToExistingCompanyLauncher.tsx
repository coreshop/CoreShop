/**
 * CoreShop Customer to Existing Company Assignment Launcher
 *
 * Transient widget that opens Element Selectors (customer → company) in sequence,
 * then immediately closes itself. On both selections, opens a persistent detail tab.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useEffect, useMemo, useState, useCallback, useRef } from 'react'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { customerCompanyApi } from './api'

const LAUNCHER_WIDGET_ID = 'coreshop-customer-to-company-assign-to-existing'

export const AssignToExistingCompanyLauncher: React.FC = () => {
  const messageApi = useMessage()
  const widgetManager = useWidgetManager()
  const [allowedCustomerClasses, setAllowedCustomerClasses] = useState<string[]>([])
  const [allowedCompanyClasses, setAllowedCompanyClasses] = useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = useState(false)
  const [pendingCompanySelect, setPendingCompanySelect] = useState(false)

  const customerIdRef = useRef<number | null>(null)
  const customerNameRef = useRef<string>('')

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const load = async () => {
      try {
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
    void load()
  }, [configProvider, messageApi])

  const handleCompanySelected = useCallback(async (companyId: number) => {
    const customerId = customerIdRef.current
    if (!customerId) return

    try {
      const response = await customerCompanyApi.getEntityDetails('company', companyId)
      const companyName = response.success && response.data ? response.data.name : `#${companyId}`

      widgetManager.openMainWidget({
        name: 'Assign ' + customerNameRef.current + ' \u2192 ' + companyName,
        id: 'coreshop-assign-existing-company-' + customerId + '-' + companyId,
        component: 'coreshop-customer-to-company-assign-to-existing-detail',
        config: { customerId, companyId },
      })
    } catch (err) {
      void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load company')))
    }
  }, [widgetManager, messageApi])

  const { open: openCompanySelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true,
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedCompanyClasses.length > 0 ? allowedCompanyClasses : undefined,
      },
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        void handleCompanySelected(selected.data.id)
      }
    },
  })

  const handleCustomerSelected = useCallback(async (customerId: number) => {
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', customerId)
      const customerName = response.success && response.data ? response.data.name : `#${customerId}`

      customerIdRef.current = customerId
      customerNameRef.current = customerName
      setPendingCompanySelect(true)
    } catch (err) {
      void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load customer')))
    }
  }, [messageApi])

  const { open: openCustomerSelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true,
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedCustomerClasses.length > 0 ? allowedCustomerClasses : undefined,
      },
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        void handleCustomerSelected(selected.data.id)
      }
    },
  })

  // Open customer selector on mount, close launcher tab
  useEffect(() => {
    if (classesLoaded) {
      openCustomerSelector()
      widgetManager.closeWidget(LAUNCHER_WIDGET_ID)
    }
  }, [classesLoaded, openCustomerSelector, widgetManager])

  // Open company selector after customer is selected
  useEffect(() => {
    if (pendingCompanySelect) {
      setPendingCompanySelect(false)
      openCompanySelector()
    }
  }, [pendingCompanySelect, openCompanySelector])

  return null
}
