/**
 * CoreShop CustomerBundle Listing Builders Module
 *
 * This module creates custom listing builders for Customers and Customer Groups
 * by copying the standard DataObject listing builder and customizing it.
 *
 * Uses ResourceConfigProvider to fetch allowed classes from CoreShop stack configuration.
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
import type { ListingBuilder } from '@pimcore/studio-ui-bundle/modules/element'
import type { ClassDefinitionSelectionDecoratorConfig } from '@pimcore/studio-ui-bundle/modules/data-object'
import { ResourceConfigProvider } from '@coreshop/resource/src/config/ConfigProvider'

export const CustomerListingBuildersModule: AbstractModule = {
  async onInit(): Promise<void> {
    try {
      // Get the standard DataObject listing builder
      const dataObjectListingBuilder = container.get<ListingBuilder>('DataObject/Listing/Builder')

      // Get ResourceConfigProvider to fetch allowed classes from stack
      const configProvider = new ResourceConfigProvider()

      // ===========================
      // Customer Listing Builder
      // ===========================
      container.bind('CoreShop/Customer/Listing/Builder').toConstantValue(dataObjectListingBuilder.copy())
      const customerListingBuilder = container.get<ListingBuilder>('CoreShop/Customer/Listing/Builder')

      // Get allowed classes for coreshop.customer from stack
      const customerClasses = await configProvider.getAllowedClasses('coreshop.customer')

      if (customerClasses.length > 0) {
        const classDefinitionSelectionDecorator = customerListingBuilder.getDecorator('classDefinitionSelection')

        if (classDefinitionSelectionDecorator !== undefined) {
          const classDefinitionSelectionDecoratorConfig: ClassDefinitionSelectionDecoratorConfig = {
            ...classDefinitionSelectionDecorator.config,
            classRestriction: customerClasses.map(className => ({ classes: className }))
          }

          customerListingBuilder.overrideDecorator({
            name: 'classDefinitionSelection',
            config: classDefinitionSelectionDecoratorConfig
          })
        }
      }

      // ===========================
      // Customer Group Listing Builder
      // ===========================
      container.bind('CoreShop/CustomerGroup/Listing/Builder').toConstantValue(dataObjectListingBuilder.copy())
      const customerGroupListingBuilder = container.get<ListingBuilder>('CoreShop/CustomerGroup/Listing/Builder')

      // Get allowed classes for coreshop.customer_group from stack
      const customerGroupClasses = await configProvider.getAllowedClasses('coreshop.customer_group')

      if (customerGroupClasses.length > 0) {
        const classDefinitionSelectionDecorator = customerGroupListingBuilder.getDecorator('classDefinitionSelection')

        if (classDefinitionSelectionDecorator !== undefined) {
          const classDefinitionSelectionDecoratorConfig: ClassDefinitionSelectionDecoratorConfig = {
            ...classDefinitionSelectionDecorator.config,
            classRestriction: customerGroupClasses.map(className => ({ classes: className }))
          }

          customerGroupListingBuilder.overrideDecorator({
            name: 'classDefinitionSelection',
            config: classDefinitionSelectionDecoratorConfig
          })
        }
      }
    } catch (err) {
      console.error('[CoreShop] Failed to initialize customer listing builders:', err)
    }
  }
}
