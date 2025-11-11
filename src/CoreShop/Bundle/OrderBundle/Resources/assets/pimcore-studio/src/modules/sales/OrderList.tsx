/**
 * CoreShop OrderBundle Order List
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

/**
 * Order List Component
 *
 * Displays CoreShopOrder DataObjects
 *
 * TODO: Integrate with Pimcore's DataObject listing
 * The ListingContainer is not exported via Module Federation,
 * so we need to either:
 * 1. Use Pimcore's widget system to open the standard DataObject listing with a filter
 * 2. Build our own listing using the DataObject API
 */
export const OrderList: React.FC = () => {
  return (
    <div style={{
      height: '100%',
      width: '100%',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexDirection: 'column',
      padding: '40px'
    }}>
      <h1 style={{ fontSize: '24px', marginBottom: '16px' }}>Orders</h1>
      <p style={{ color: '#666', textAlign: 'center', maxWidth: '600px' }}>
        Order listing will be displayed here.
      </p>
      <p style={{ color: '#999', fontSize: '14px', marginTop: '8px' }}>
        Navigate to DataObjects → CoreShopOrder to view orders
      </p>
    </div>
  )
}
