/**
 * CoreShop PimcoreBundle Grid Filter Decorator
 *
 * Decorator that adds filter support to a listing builder.
 * The filter is passed to the backend via session storage mechanism.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { AbstractDecorator, AbstractDecoratorProps } from '@pimcore/studio-ui-bundle/modules/element'

export interface GridFilterDecoratorConfig {
  listType: string
}

// Storage key prefix for filter values
const FILTER_STORAGE_KEY_PREFIX = 'coreshop_grid_filter_'

/**
 * Get the storage key for a list type
 */
export const getFilterStorageKey = (listType: string): string => {
  return `${FILTER_STORAGE_KEY_PREFIX}${listType}`
}

/**
 * Get the current filter value for a list type from session storage
 */
export const getStoredFilter = (listType: string): string | null => {
  if (typeof sessionStorage === 'undefined') return null
  return sessionStorage.getItem(getFilterStorageKey(listType))
}

/**
 * Store the filter value for a list type in session storage
 */
export const setStoredFilter = (listType: string, filterId: string | null): void => {
  if (typeof sessionStorage === 'undefined') return
  const key = getFilterStorageKey(listType)
  if (filterId === null) {
    sessionStorage.removeItem(key)
  } else {
    sessionStorage.setItem(key, filterId)
  }
}

/**
 * Creates a decorator that enables filter support for a listing
 *
 * The filter value is stored in sessionStorage and can be read by the backend
 * through the ObjectListFilterListener which reads 'coreshop_filter' from context.
 *
 * Note: The actual filter parameter injection into API calls needs to be handled
 * at the API layer or through Pimcore's request context mechanism.
 *
 * @example
 * const filterDecorator = createGridFilterDecorator({ listType: 'coreshop_order' })
 * listingBuilder.addDecorator({ name: 'gridFilter', decorator: filterDecorator })
 */
export const createGridFilterDecorator = (config: GridFilterDecoratorConfig): AbstractDecorator => {
  return (props: AbstractDecoratorProps): AbstractDecoratorProps => {
    const { useGridOptions, ...defaultProps } = props

    const newUseGridOptions: AbstractDecoratorProps['useGridOptions'] = () => {
      const baseOptions = useGridOptions()

      return {
        ...baseOptions,
        // The filter handling is done through the wrapper component approach
        // since Pimcore's listing API doesn't directly expose parameter injection
        getGridProps: () => {
          const baseGridProps = baseOptions.getGridProps()

          return {
            ...baseGridProps,
            // Store list type in data attribute for reference
            'data-coreshop-list-type': config.listType
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
