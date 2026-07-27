/**
 * CoreShop OrderBundle Service IDs
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export const serviceIds = {
  // Tab Registry
  saleTabRegistry: Symbol.for('coreshop.order.sale_tab_registry'),

  // API Instances
  orderApi: Symbol.for('coreshop.order.order_api'),
  cartApi: Symbol.for('coreshop.order.cart_api'),
  quoteApi: Symbol.for('coreshop.order.quote_api'),
}
