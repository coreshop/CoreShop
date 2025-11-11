/**
 * CoreShop OrderBundle Cart Manager
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
import { SaleManager } from './SaleManager'

export const CartManager: React.FC = () => {
  return <SaleManager type="cart" />
}
