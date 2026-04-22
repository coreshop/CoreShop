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
import { SettingsManager } from './SettingsManager'

export { SettingsManager } from './SettingsManager'
export { settingsApi } from './api'

export const SettingsModule: AbstractModule = {
  onInit(): void {
    const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetRegistryService.registerWidget({
      name: 'coreshop-settings-widget',
      component: SettingsManager
    })
  }
}
