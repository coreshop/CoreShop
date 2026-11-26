/**
 * CoreShop CoreBundle - Money Widget Module
 *
 * Registers the 'coreshop_money' block prefix (MoneyBundle MoneyType) so schema forms (rule conditions and
 * actions) show money as decimal values while keeping the stored integer.
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
import { MoneyWidget } from './MoneyWidget'

export const MoneyWidgetModule: AbstractModule = {
  onInit(): void {
    const widgetRegistry = container.get<WidgetRegistry>(widgetRegistryServiceId)

    widgetRegistry.register('coreshop_money', () => ({
      component: MoneyWidget,
    }))
  }
}
