/**
 * CoreShop PimcoreBundle Grid Actions Decorator
 *
 * Decorator that adds action support (context menu, row selection) to a listing builder.
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

export interface GridActionsDecoratorConfig {
  listType: string
  openHandler: (id: number) => void
}

/**
 * Creates a decorator that enables action support for a listing
 *
 * Features:
 * - Row double-click to open item
 * - Row selection for batch operations
 * - Context menu support (implemented via GridActionsMenu component)
 *
 * @example
 * const actionsDecorator = createGridActionsDecorator({
 *   listType: 'coreshop_order',
 *   openHandler: (id) => openOrderDetail(id)
 * })
 * listingBuilder.overrideDecorator({ name: 'actionColumn', decorator: actionsDecorator })
 */
export const createGridActionsDecorator = (config: GridActionsDecoratorConfig): AbstractDecorator => {
  return (props: AbstractDecoratorProps): AbstractDecoratorProps => {
    const { useGridOptions, ...defaultProps } = props

    const newUseGridOptions: AbstractDecoratorProps['useGridOptions'] = () => {
      const baseOptions = useGridOptions()

      return {
        ...baseOptions,
        getGridProps: () => {
          const baseGridProps = baseOptions.getGridProps()

          return {
            ...baseGridProps,
            // Enable row double-click
            onRowDoubleClick: (row: any) => {
              config.openHandler(row.id)
            },
            // Enable row selection for batch operations
            enableRowSelection: true,
            enableMultipleRowSelection: true,
            // Store list type for context menu reference
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
