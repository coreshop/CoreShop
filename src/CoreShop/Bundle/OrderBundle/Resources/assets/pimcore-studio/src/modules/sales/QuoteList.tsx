/**
 * CoreShop OrderBundle Quote List
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
 * Quote List Component
 *
 * Displays CoreShopQuote DataObjects
 */
export const QuoteList: React.FC = () => {
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
      <h1 style={{ fontSize: '24px', marginBottom: '16px' }}>Quotes</h1>
      <p style={{ color: '#666', textAlign: 'center', maxWidth: '600px' }}>
        Quote listing will be displayed here.
      </p>
      <p style={{ color: '#999', fontSize: '14px', marginTop: '8px' }}>
        Navigate to DataObjects → CoreShopQuote to view quotes
      </p>
    </div>
  )
}
