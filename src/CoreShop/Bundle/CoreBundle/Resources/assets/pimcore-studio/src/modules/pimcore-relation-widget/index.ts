/**
 * CoreShop CoreBundle - Pimcore Relation Widget Module
 *
 * Registers the 'autocomplete' widget type to render Pimcore's
 * ManyToManyRelation / ManyToOneRelation components in schema-based forms.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { AbstractModule } from '@pimcore/studio-ui-bundle'
import { container } from '@pimcore/studio-ui-bundle'
import { widgetRegistryServiceId } from '@coreshop/studio-form'
import type { WidgetRegistry } from '@coreshop/studio-form'
import { PimcoreRelationWidget } from './PimcoreRelationWidget'

export const PimcoreRelationWidgetModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

    widgetRegistry.register('autocomplete', (field) => ({
      component: PimcoreRelationWidget,
      props: {
        autocompleteClass: field.uiType.autocompleteClass,
        multiple: field.uiType.multiple ?? false,
      },
    }))
  }
}
