/**
 * CoreShop OrderBundle - Order Creation Launcher Component
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
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
import { orderCreationApi } from '../api'

const LAUNCHER_WIDGET_ID = 'coreshop-order-creation'

/**
 * OrderCreationLauncher is a transient widget that opens the Pimcore Element Selector
 * for customer selection, then immediately closes itself.
 * On customer selection, it opens a persistent "order creation detail" tab.
 */
export const OrderCreationLauncher: React.FC = () => {
  const messageApi = useMessage()
  const widgetManager = useWidgetManager()
  const [allowedClasses, setAllowedClasses] = useState<string[]>([])
  const [classesLoaded, setClassesLoaded] = useState(false)

  const configProvider = useMemo(
    () => container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider),
    []
  )

  // Load allowed customer classes
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
      const details = await orderCreationApi.getCustomerDetails(customerId)
      const customerName = [details.firstname, details.lastname].filter(Boolean).join(' ') || `Customer #${customerId}`

      widgetManager.openMainWidget({
        name: `New Order - ${customerName}`,
        id: `coreshop-order-creation-${customerId}`,
        component: 'coreshop-order-creation-detail',
        config: {
          customerId
        }
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
        void handleCustomerSelected(selected.data.id)
      }
    }
  })

  // Open the element selector as soon as classes are loaded, then close the launcher tab
  useEffect(() => {
    if (classesLoaded) {
      openSelector()
      // Close the launcher widget tab — it's just a transient trigger
      widgetManager.closeWidget(LAUNCHER_WIDGET_ID)
    }
  }, [classesLoaded, openSelector, widgetManager])

  // Render nothing meaningful — this widget closes itself immediately
  return null
}
