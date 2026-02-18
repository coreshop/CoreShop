/**
 * CoreShop Customer to New Company Assignment Launcher
 *
 * Transient widget that opens the Element Selector for customer selection,
 * then immediately closes itself. On selection, opens a persistent detail tab.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useEffect, useMemo, useState, useCallback } from 'react'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { customerCompanyApi } from './api'

const LAUNCHER_WIDGET_ID = 'coreshop-customer-to-company-assign-to-new'

export const AssignToNewCompanyLauncher: React.FC = () => {
  const messageApi = useMessage()
  const widgetManager = useWidgetManager()
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = useState(false)

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  useEffect(() => {
    const load = async () => {
      try {
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedClasses(classes)
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed customer classes')))
        setAllowedClasses(['CoreShopCustomer'])
      } finally {
        setClassesLoaded(true)
      }
    }
    void load()
  }, [configProvider, messageApi])

  const handleCustomerSelected = useCallback(async (customerId: number) => {
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', customerId)
      const customerName = response.success && response.data ? response.data.name : `#${customerId}`

      widgetManager.openMainWidget({
        name: 'Assign to New Company - ' + customerName,
        id: 'coreshop-assign-new-company-' + customerId,
        component: 'coreshop-customer-to-company-assign-to-new-detail',
        config: { customerId },
      })
    } catch (err) {
      void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load customer')))
    }
  }, [widgetManager, messageApi])

  const { open: openSelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true,
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedClasses.length > 0 ? allowedClasses : undefined,
      },
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        void handleCustomerSelected(selected.data.id)
      }
    },
  })

  useEffect(() => {
    if (classesLoaded) {
      openSelector()
      widgetManager.closeWidget(LAUNCHER_WIDGET_ID)
    }
  }, [classesLoaded, openSelector, widgetManager])

  return null
}
