/**
 * CoreShop MoneyBundle Studio Plugin
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
import {
  DynamicTypeObjectDataRegistry
} from '@pimcore/studio-ui-bundle/modules/element'
import { MoneyBundleIconModule } from './modules/icon-library'
import { DynamicTypeObjectDataCoreShopMoney } from './dynamic-types/DynamicTypeObjectDataCoreShopMoney'
import { registerCoreShopFieldDefinitionTypes } from '@coreshop/pimcore/src/dynamic-types/field-definitions'
import {
    DynamicTypeFieldDefinitionCoreShopMoney
} from './dynamic-types'

const plugin: IAbstractPlugin = {
    name: 'coreshop-money',

    onInit() {
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(
          'DynamicTypes/ObjectDataRegistry'
        )

        objectDataRegistry.registerDynamicType(new DynamicTypeObjectDataCoreShopMoney())

        // Class-definition editor types (Studio class editor)
        registerCoreShopFieldDefinitionTypes([
            new DynamicTypeFieldDefinitionCoreShopMoney()
        ])
    },

    onStartup({ moduleSystem }) {
        moduleSystem.registerModule(MoneyBundleIconModule)
    }
}

export default plugin
