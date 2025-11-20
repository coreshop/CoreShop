/**
 * CoreShop OrderBundle Cart List
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { BaseListing, DataObjectProvider, listingDefaultProps, type ObjectListingBuilder } from '@pimcore/studio-ui-bundle/modules/data-object'

/**
 * Cart List Component
 *
 * Displays CoreShopCart DataObjects using Pimcore's DataObject listing
 */
export const CartList: React.FC = () => {
  const listingBuilder = container.get<ObjectListingBuilder>('CoreShop/Cart/Listing/Builder')

  return (
    <DataObjectProvider id={1}>
      <BaseListing
        {...listingBuilder.build({
          props: {
            ...listingDefaultProps
          },
          config: {}
        })}
      />
    </DataObjectProvider>
  )
}
