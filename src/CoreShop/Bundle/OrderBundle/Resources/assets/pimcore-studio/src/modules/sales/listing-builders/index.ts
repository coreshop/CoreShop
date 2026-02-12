/**
 * CoreShop OrderBundle Sales Listing Builders Module
 *
 * This module creates custom listing builders for Orders, Carts, and Quotes
 * by copying the standard DataObject listing builder and customizing it.
 *
 * Uses ResourceConfigProvider to fetch allowed classes from CoreShop stack configuration.
 *
 * Based on: https://github.com/pimcore/studio-example-bundle/blob/main/assets/js/src/examples/listings/modules/custom-data-object-listing.tsx
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
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { type AbstractDecorator, type AbstractDecoratorProps } from '@pimcore/studio-ui-bundle/modules/element'
import { createPresetFilterDecorator } from '@coreshop/pimcore/src/modules/grid/decorators/PresetFilterDecorator'
import { openSaleWidget } from '../hooks'
import type { SaleType } from '../types'

/**
 * Creates a custom decorator that adds onRowDoubleClick functionality
 * Uses centralized openSaleWidget utility for opening sale details
 */
const createRowDoubleClickDecorator = (saleType: SaleType): AbstractDecorator => {
  return (props: AbstractDecoratorProps): AbstractDecoratorProps => {
    const { useGridOptions, ...defaultProps } = props

    const newUseGridOptions: AbstractDecoratorProps['useGridOptions'] = () => {
      const baseOptions = useGridOptions()
      const widgetManager = useWidgetManager()

      return {
        ...baseOptions,
        getGridProps: () => {
          const baseGridProps = baseOptions.getGridProps()

          return {
            ...baseGridProps,
            onRowDoubleClick: (row: any) => {
              openSaleWidget(widgetManager, { id: row.id, type: saleType })
            }
          }
        }
      }
    }

    return {
      ...defaultProps,
      useGridOptions: newUseGridOptions
    }
  }
}

export const SalesListingBuildersModule: AbstractModule = {
  async onInit(): Promise<void> {
    try {
      // Get the standard DataObject listing builder
      const dataObjectListingBuilder = container.get<ListingBuilder>('DataObject/Listing/Builder')

      // Get ResourceConfigProvider to fetch allowed classes from stack
      const configProvider = new ResourceConfigProvider()

      // ===========================
      // Order Listing Builder
      // ===========================
      container.bind('CoreShop/Order/Listing/Builder').toConstantValue(dataObjectListingBuilder.copy())
      const orderListingBuilder = container.get<ListingBuilder>('CoreShop/Order/Listing/Builder')

      // Get allowed classes for coreshop.order from stack
      const orderClasses = await configProvider.getAllowedClasses('coreshop.order')

      if (orderClasses.length > 0) {
        const classDefinitionSelectionDecorator = orderListingBuilder.getDecorator('classDefinitionSelection')

        if (classDefinitionSelectionDecorator !== undefined) {
          const classDefinitionSelectionDecoratorConfig: ClassDefinitionSelectionDecoratorConfig = {
            ...classDefinitionSelectionDecorator.config,
            classRestriction: orderClasses.map(className => ({ classes: className }))
          }

          orderListingBuilder.overrideDecorator({
            name: 'classDefinitionSelection',
            config: classDefinitionSelectionDecoratorConfig
          })
        }
      }

      // Override actionColumn decorator to add onRowDoubleClick for opening Order detail widget
      orderListingBuilder.overrideDecorator({
        name: 'actionColumn',
        decorator: createRowDoubleClickDecorator('order')
      })

      // Add preset filter decorator for Studio v2 filter support
      orderListingBuilder.addDecorator({
        name: 'presetFilter',
        decorator: createPresetFilterDecorator({ listType: 'coreshop_order' })
      })

      // ===========================
      // Cart Listing Builder
      // ===========================
      container.bind('CoreShop/Cart/Listing/Builder').toConstantValue(dataObjectListingBuilder.copy())
      const cartListingBuilder = container.get<ListingBuilder>('CoreShop/Cart/Listing/Builder')

      // Get allowed classes for coreshop.cart from stack
      const cartClasses = await configProvider.getAllowedClasses('coreshop.cart')

      if (cartClasses.length > 0) {
        const classDefinitionSelectionDecorator = cartListingBuilder.getDecorator('classDefinitionSelection')

        if (classDefinitionSelectionDecorator !== undefined) {
          const classDefinitionSelectionDecoratorConfig: ClassDefinitionSelectionDecoratorConfig = {
            ...classDefinitionSelectionDecorator.config,
            classRestriction: cartClasses.map(className => ({ classes: className }))
          }

          cartListingBuilder.overrideDecorator({
            name: 'classDefinitionSelection',
            config: classDefinitionSelectionDecoratorConfig
          })
        }
      }

      // Override actionColumn decorator to add onRowDoubleClick for opening Cart detail widget
      cartListingBuilder.overrideDecorator({
        name: 'actionColumn',
        decorator: createRowDoubleClickDecorator('cart')
      })

      // Add preset filter decorator for Studio v2 filter support
      cartListingBuilder.addDecorator({
        name: 'presetFilter',
        decorator: createPresetFilterDecorator({ listType: 'coreshop_cart' })
      })

      // ===========================
      // Quote Listing Builder
      // ===========================
      container.bind('CoreShop/Quote/Listing/Builder').toConstantValue(dataObjectListingBuilder.copy())
      const quoteListingBuilder = container.get<ListingBuilder>('CoreShop/Quote/Listing/Builder')

      // Get allowed classes for coreshop.quote from stack
      const quoteClasses = await configProvider.getAllowedClasses('coreshop.quote')

      if (quoteClasses.length > 0) {
        const classDefinitionSelectionDecorator = quoteListingBuilder.getDecorator('classDefinitionSelection')

        if (classDefinitionSelectionDecorator !== undefined) {
          const classDefinitionSelectionDecoratorConfig: ClassDefinitionSelectionDecoratorConfig = {
            ...classDefinitionSelectionDecorator.config,
            classRestriction: quoteClasses.map(className => ({ classes: className }))
          }

          quoteListingBuilder.overrideDecorator({
            name: 'classDefinitionSelection',
            config: classDefinitionSelectionDecoratorConfig
          })
        }
      }

      // Override actionColumn decorator to add onRowDoubleClick for opening Quote detail widget
      quoteListingBuilder.overrideDecorator({
        name: 'actionColumn',
        decorator: createRowDoubleClickDecorator('quote')
      })

      // Add preset filter decorator for Studio v2 filter support
      quoteListingBuilder.addDecorator({
        name: 'presetFilter',
        decorator: createPresetFilterDecorator({ listType: 'coreshop_quote' })
      })
    } catch (err) {
      console.error('[CoreShop] Failed to initialize sales listing builders:', err)
    }
  }
}
