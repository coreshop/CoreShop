/**
 * CoreShop StudioFormBundle - Plugin Entry Point
 *
 * Registers the WidgetRegistry and default widgets.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { container, type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { WidgetRegistry } from './schema-adapter/WidgetRegistry'
import { registerDefaultWidgets } from './schema-adapter/defaultWidgets'
import { widgetRegistryServiceId } from './services'
import { DemoModule } from './modules/demo'

const plugin: IAbstractPlugin = {
  name: 'coreshop-studio-form-plugin',

  // Init before the feature bundle plugins (index, address, ...) that resolve the
  // StudioForm WidgetRegistry in their own onInit. Plugins are ordered ascending by
  // priority, so a low value guarantees this plugin binds the registry first.
  priority: -1000,

  onInit() {
    // Bind WidgetRegistry as singleton
    container.bind(widgetRegistryServiceId).to(WidgetRegistry).inSingletonScope()

    // Register default widgets (input, textarea, inputNumber, switch, select, etc.)
    const registry = container.get<WidgetRegistry>(widgetRegistryServiceId)
    registerDefaultWidgets(registry)
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(DemoModule)
  },
}

export default plugin
