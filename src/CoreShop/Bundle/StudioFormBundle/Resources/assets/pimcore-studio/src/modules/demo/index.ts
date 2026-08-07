/**
 * CoreShop StudioFormBundle - Demo Module
 *
 * Registers the demo showcase widget and its menu entry.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DemoShowcase } from './DemoShowcase'

const WIDGET_ID = 'coreshop-studio-form-demos'

export const DemoModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(serviceIds.widgetManager)

    widgetRegistry.registerWidget({
      name: WIDGET_ID,
      component: DemoShowcase,
    })
  },
}
