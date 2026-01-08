/**
 * CoreShop CoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { ReportsManager } from './components/ReportsManager'

export { ReportsManager } from './components/ReportsManager'
export { ReportPanel } from './components/ReportPanel'
export { ReportFilters } from './components/ReportFilters'
export { SalesReport } from './components/SalesReport'
export { ProductsReport } from './components/ProductsReport'
export { CustomersReport } from './components/CustomersReport'
export { reportsApi } from './api'
export type * from './types'

export const ReportsModule: AbstractModule = {
  onInit(): void {
    const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetRegistryService.registerWidget({
      name: 'coreshop-reports-widget',
      component: ReportsManager
    })
  }
}
