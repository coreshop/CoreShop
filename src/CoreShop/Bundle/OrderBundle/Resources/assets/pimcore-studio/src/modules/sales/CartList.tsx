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

/**
 * Cart List Component
 *
 * Displays CoreShopCart DataObjects
 */
export const CartList: React.FC = () => {
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
      <h1 style={{ fontSize: '24px', marginBottom: '16px' }}>Carts</h1>
      <p style={{ color: '#666', textAlign: 'center', maxWidth: '600px' }}>
        Cart listing will be displayed here.
      </p>
      <p style={{ color: '#999', fontSize: '14px', marginTop: '8px' }}>
        Navigate to DataObjects → CoreShopCart to view carts
      </p>
    </div>
  )
}
