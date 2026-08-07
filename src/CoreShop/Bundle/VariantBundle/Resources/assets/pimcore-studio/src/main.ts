/**
 * CoreShop VariantBundle Studio Plugin
 *
 * Provides a "Generate Variants" button in the data object editor toolbar
 * for objects whose class is in the variant_aware stack.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { ComponentRegistryInterface } from '@pimcore/studio-ui-bundle/modules/app'
import { VariantBundleIconModule } from './modules/icon-library'
import { GenerateVariantsToolbarButton } from './modules/variant-generator/GenerateVariantsToolbarButton'

const plugin: IAbstractPlugin = {
  name: 'coreshop-variant',

  onInit() {
    // Register "Generate Variants" toolbar button in data object editor toolbar (left side)
    const componentRegistry = container.get<ComponentRegistryInterface>(
      serviceIds['App/ComponentRegistry/ComponentRegistry']
    )

    componentRegistry.registerToSlot('dataObject.editor.toolbar.slots.left', {
      name: 'coreshop-generate-variants',
      priority: 150, // After context menu (100), before language selection (200)
      component: GenerateVariantsToolbarButton
    })
  },

  onStartup({ moduleSystem }) {
    moduleSystem.registerModule(VariantBundleIconModule)
  }
}

export default plugin
