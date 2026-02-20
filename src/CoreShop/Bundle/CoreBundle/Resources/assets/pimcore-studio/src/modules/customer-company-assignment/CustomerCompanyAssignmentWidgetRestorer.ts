/**
 * CoreShop Customer Company Assignment Widget Restorer
 *
 * Handles persistence and restoration of assignment detail widgets
 * across browser refreshes.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { WidgetManagerTabConfig } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import type { AppDispatch } from '@pimcore/studio-ui-bundle/app'

interface WidgetRestorer {
  supports(config: WidgetManagerTabConfig): boolean
  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig
  restore(config: WidgetManagerTabConfig, dispatch: AppDispatch): boolean
}

const SUPPORTED_COMPONENTS = [
  'coreshop-customer-to-company-assign-to-new-detail',
  'coreshop-customer-to-company-assign-to-existing-detail',
]

export class CustomerCompanyAssignmentWidgetRestorer implements WidgetRestorer {
  supports(config: WidgetManagerTabConfig): boolean {
    return (
      config.component !== undefined && SUPPORTED_COMPONENTS.includes(config.component) &&
      typeof config.config?.customerId === 'number'
    )
  }

  cleanConfig(config: WidgetManagerTabConfig): WidgetManagerTabConfig {
    if (config.component === 'coreshop-customer-to-company-assign-to-existing-detail') {
      return {
        ...config,
        config: {
          customerId: config.config?.customerId,
          companyId: config.config?.companyId,
        },
      }
    }

    return {
      ...config,
      config: {
        customerId: config.config?.customerId,
      },
    }
  }

  restore(config: WidgetManagerTabConfig, _dispatch: AppDispatch): boolean {
    if (config.component === 'coreshop-customer-to-company-assign-to-existing-detail') {
      return (
        typeof config.config?.customerId === 'number' &&
        config.config.customerId > 0 &&
        typeof config.config?.companyId === 'number' &&
        config.config.companyId > 0
      )
    }

    return typeof config.config?.customerId === 'number' && config.config.customerId > 0
  }
}

export const customerCompanyAssignmentWidgetRestorer = new CustomerCompanyAssignmentWidgetRestorer()
