/**
 * CoreShop CustomerBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { IAbstractPlugin, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { CustomerBundleIconModule } from './modules/icon-library'
import { CustomerListingBuildersModule } from './modules/listing-builders'
import { CustomerList } from './modules/customers'
import { CustomerGroupList } from './modules/customer-groups'

const plugin: IAbstractPlugin = {
    name: 'coreshop-customer-plugin',

    onInit() {
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(CustomerBundleIconModule)
        moduleSystem.registerModule(CustomerListingBuildersModule)

        // Register widgets
        const widgets = container.get<WidgetRegistry>(serviceIds.widgetManager)

        // Register Customer List widget
        widgets.registerWidget({
            name: 'coreshop-customer-customers',
            component: CustomerList
        })

        // Register Customer Group List widget
        widgets.registerWidget({
            name: 'coreshop-customer-customer-groups',
            component: CustomerGroupList
        })
    }
}

export default plugin
