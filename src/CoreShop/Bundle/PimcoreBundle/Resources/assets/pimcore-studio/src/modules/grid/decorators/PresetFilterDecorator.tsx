/**
 * CoreShop PimcoreBundle Grid Filter Decorator
 *
 * Listing decorator that adds the selected filter as a columnFilter
 * to the grid's data query.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractDecoratorProps } from '@pimcore/studio-ui-bundle/modules/element'
import { usePresetFilterOptional } from '../context/PresetFilterContext'

export interface PresetFilterDecoratorConfig {
  /**
   * The list type for this listing (e.g., 'coreshop_order', 'coreshop_cart')
   */
  listType: string
}

/**
 * Creates a decorator that adds the selected filter as a columnFilter to the query.
 *
 * The selected filter type (e.g., 'coreshop_created_today') is sent directly
 * as the columnFilter type. Each filter registered with Pimcore's filter system
 * checks if its type is present and applies itself.
 *
 * @param config Configuration with listType
 */
export const createPresetFilterDecorator = (config: PresetFilterDecoratorConfig) => {
  return (props: AbstractDecoratorProps): AbstractDecoratorProps => {
    const { useDataQueryHelper, ...defaultProps } = props

    const newUseDataQueryHelper: AbstractDecoratorProps['useDataQueryHelper'] = () => {
      const baseHelper = useDataQueryHelper()
      const filterContext = usePresetFilterOptional()

      return {
        ...baseHelper,
        getArgs: () => {
          const baseArgs = baseHelper.getArgs()
          const selectedFilter = filterContext?.selectedFilter

          if (!selectedFilter) {
            return baseArgs
          }

          const existingColumnFilters = baseArgs.body?.filters?.columnFilters ?? []

          // Send the filter type directly - each filter checks for its own type
          const columnFilter = {
            type: selectedFilter
          }

          return {
            ...baseArgs,
            body: {
              ...baseArgs.body,
              filters: {
                ...baseArgs.body?.filters,
                columnFilters: [
                  ...existingColumnFilters,
                  columnFilter
                ]
              }
            }
          }
        }
      }
    }

    return {
      ...defaultProps,
      useDataQueryHelper: newUseDataQueryHelper
    }
  }
}

export default createPresetFilterDecorator
